using System;
using System.Collections.Concurrent;
using System.Collections.Generic;
using System.Diagnostics;
using System.Linq;
using System.Net.Sockets;
using System.Threading;
using System.Threading.Tasks;

namespace NetworkToolPro.Services;

public sealed class PortScannerService
{
    private readonly TimeSpan _defaultTimeout = TimeSpan.FromSeconds(1);

    public async Task<IReadOnlyList<PortScanResult>> ScanAsync(
        string host,
        IReadOnlyList<int> ports,
        TimeSpan? timeout = null,
        int maxConcurrency = 100,
        IProgress<PortScanProgress>? progress = null,
        CancellationToken cancellationToken = default)
    {
        if (string.IsNullOrWhiteSpace(host))
        {
            throw new ArgumentException("Host must be provided", nameof(host));
        }

        if (ports.Count == 0)
        {
            throw new ArgumentException("At least one port must be provided", nameof(ports));
        }

        var actualTimeout = timeout.GetValueOrDefault(_defaultTimeout);
        var results = new ConcurrentBag<PortScanResult>();
        using var semaphore = new SemaphoreSlim(Math.Max(1, maxConcurrency));
        var tasks = new List<Task>();
        var stopwatch = Stopwatch.StartNew();

        foreach (var port in ports)
        {
            cancellationToken.ThrowIfCancellationRequested();

            await semaphore.WaitAsync(cancellationToken).ConfigureAwait(false);

            tasks.Add(Task.Run(async () =>
            {
                try
                {
                    var singleResult = await ProbePortAsync(host, port, actualTimeout, cancellationToken)
                        .ConfigureAwait(false);
                    results.Add(singleResult);

                    progress?.Report(new PortScanProgress(ports.Count, results.Count, singleResult));
                }
                finally
                {
                    semaphore.Release();
                }
            }, cancellationToken));
        }

        await Task.WhenAll(tasks).ConfigureAwait(false);
        stopwatch.Stop();

        var ordered = results.OrderBy(r => r.Port).ToArray();
        progress?.Report(new PortScanProgress(ports.Count, ordered.Length, null, stopwatch.Elapsed));
        return ordered;
    }

    private static async Task<PortScanResult> ProbePortAsync(
        string host,
        int port,
        TimeSpan timeout,
        CancellationToken cancellationToken)
    {
        var result = new PortScanResult(port);
        var stopwatch = Stopwatch.StartNew();

        try
        {
            using var cts = CancellationTokenSource.CreateLinkedTokenSource(cancellationToken);
            cts.CancelAfter(timeout);

            using var client = new TcpClient();
            var connectTask = client.ConnectAsync(host, port);
            var completedTask = await Task.WhenAny(connectTask, Task.Delay(Timeout.Infinite, cts.Token))
                .ConfigureAwait(false);

            if (completedTask != connectTask)
            {
                result = result with
                {
                    Status = PortStatus.Closed,
                    Notes = "Timed out"
                };
            }
            else
            {
                await connectTask.ConfigureAwait(false);
                result = result with
                {
                    Status = client.Connected ? PortStatus.Open : PortStatus.Closed,
                    Notes = client.Connected ? "Connected successfully" : "Connection failed"
                };
            }
        }
        catch (SocketException ex)
        {
            result = result with
            {
                Status = PortStatus.Closed,
                Notes = ex.SocketErrorCode.ToString()
            };
        }
        catch (OperationCanceledException)
        {
            result = result with
            {
                Status = PortStatus.Unknown,
                Notes = "Cancelled"
            };
        }
        catch (Exception ex)
        {
            result = result with
            {
                Status = PortStatus.Unknown,
                Notes = ex.Message
            };
        }
        finally
        {
            stopwatch.Stop();
            result = result with { ResponseTime = stopwatch.Elapsed };
        }

        return result;
    }
}

public enum PortStatus
{
    Unknown,
    Open,
    Closed
}

public sealed record PortScanResult(int Port)
{
    public PortStatus Status { get; init; } = PortStatus.Unknown;
    public TimeSpan ResponseTime { get; init; } = TimeSpan.Zero;
    public string Notes { get; init; } = string.Empty;
}

public sealed record PortScanProgress(
    int TotalPorts,
    int PortsCompleted,
    PortScanResult? LatestResult,
    TimeSpan? TotalElapsed = null);

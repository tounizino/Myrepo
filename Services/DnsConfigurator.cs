using System;
using System.Collections.Generic;
using System.Linq;
using System.Management;
using System.Threading;
using System.Threading.Tasks;

namespace NetworkToolPro.Services;

public sealed class DnsConfigurator
{
    public IReadOnlyList<DnsAdapterInfo> GetConfigurableAdapters()
    {
        var adapters = new List<DnsAdapterInfo>();

        using var searcher = new ManagementObjectSearcher(
            "SELECT * FROM Win32_NetworkAdapterConfiguration WHERE IPEnabled = TRUE");

        foreach (ManagementObject adapter in searcher.Get())
        {
            var adapterInfo = new DnsAdapterInfo
            {
                Caption = adapter["Caption"]?.ToString() ?? "Unknown Adapter",
                Description = adapter["Description"]?.ToString() ?? "Unknown",
                InterfaceIndex = Convert.ToInt32(adapter["InterfaceIndex"] ?? -1),
                SettingId = adapter["SettingID"]?.ToString() ?? string.Empty,
                DhcpEnabled = Convert.ToBoolean(adapter["DHCPEnabled"] ?? false),
                IpAddresses = ((string[]?)adapter["IPAddress"])?.ToList() ?? new List<string>(),
                DnsServers = ((string[]?)adapter["DNSServerSearchOrder"])?.ToList() ?? new List<string>()
            };

            adapters.Add(adapterInfo);
        }

        return adapters
            .OrderByDescending(a => a.DnsServers.Count > 0)
            .ThenBy(a => a.Caption)
            .ToList();
    }

    public async Task<DnsOperationResult> SetDnsServersAsync(
        DnsAdapterInfo adapter,
        IReadOnlyList<string> dnsServers,
        CancellationToken cancellationToken = default)
    {
        return await Task.Run(() =>
        {
            using var searcher = new ManagementObjectSearcher(
                $"SELECT * FROM Win32_NetworkAdapterConfiguration WHERE InterfaceIndex = {adapter.InterfaceIndex}");

            foreach (ManagementObject managementObject in searcher.Get())
            {
                if (!(bool)(managementObject["IPEnabled"] ?? false))
                    continue;

                using var newDns = managementObject.GetMethodParameters("SetDNSServerSearchOrder");
                newDns["DNSServerSearchOrder"] = dnsServers.Count == 0 ? null : dnsServers.ToArray();

                var result = managementObject.InvokeMethod("SetDNSServerSearchOrder", newDns, null);
                var returnValue = Convert.ToInt32(result?["ReturnValue"] ?? 1);

                return new DnsOperationResult(returnValue == 0, returnValue, DescribeReturnCode(returnValue));
            }

            return new DnsOperationResult(false, -1, "Adapter not found or not IP enabled");
        }, cancellationToken).ConfigureAwait(false);
    }

    private static string DescribeReturnCode(int code) => code switch
    {
        0 => "Success",
        1 => "No change required",
        64 => "Adapter not found",
        65 => "Adapter not configurable",
        67 => "Invalid parameter",
        68 => "System is busy",
        70 => "Invalid environment",
        _ => $"Operation returned code {code}"
    };
}

public sealed record DnsAdapterInfo
{
    public string Caption { get; init; } = string.Empty;
    public string Description { get; init; } = string.Empty;
    public int InterfaceIndex { get; init; }
    public string SettingId { get; init; } = string.Empty;
    public bool DhcpEnabled { get; init; }
    public List<string> IpAddresses { get; init; } = new();
    public List<string> DnsServers { get; init; } = new();

    public override string ToString() =>
        string.IsNullOrWhiteSpace(Description) ? Caption : $"{Caption} ({Description})";
}

public sealed record DnsOperationResult(bool IsSuccess, int ReturnCode, string Message);

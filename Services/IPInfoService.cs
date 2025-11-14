using System;
using System.Collections.Generic;
using System.Linq;
using System.Net;
using System.Net.Http;
using System.Net.NetworkInformation;
using System.Net.Sockets;
using System.Threading.Tasks;

namespace NetworkToolPro.Services;

public sealed class IPInfoService
{
    public async Task<NetworkInformation> GetNetworkInformationAsync()
    {
        var info = new NetworkInformation();

        try
        {
            info.HostName = Dns.GetHostName();

            var hostEntry = await Dns.GetHostEntryAsync(info.HostName);

            info.LocalIPv4Addresses = hostEntry.AddressList
                .Where(ip => ip.AddressFamily == AddressFamily.InterNetwork)
                .Select(ip => ip.ToString())
                .ToList();

            info.LocalIPv6Addresses = hostEntry.AddressList
                .Where(ip => ip.AddressFamily == AddressFamily.InterNetworkV6)
                .Select(ip => ip.ToString())
                .ToList();

            var interfaces = NetworkInterface.GetAllNetworkInterfaces()
                .Where(ni => ni.OperationalStatus == OperationalStatus.Up &&
                            ni.NetworkInterfaceType != NetworkInterfaceType.Loopback)
                .ToArray();

            var adapters = new List<NetworkAdapterInfo>();

            foreach (var ni in interfaces)
            {
                var adapter = new NetworkAdapterInfo
                {
                    Name = ni.Name,
                    Description = ni.Description,
                    Status = ni.OperationalStatus.ToString(),
                    Speed = FormatSpeed(ni.Speed),
                    Type = ni.NetworkInterfaceType.ToString(),
                    MacAddress = BitConverter.ToString(ni.GetPhysicalAddress().GetAddressBytes())
                };

                var ipProps = ni.GetIPProperties();

                adapter.IPv4Addresses = ipProps.UnicastAddresses
                    .Where(ua => ua.Address.AddressFamily == AddressFamily.InterNetwork)
                    .Select(ua => ua.Address.ToString())
                    .ToList();

                adapter.IPv6Addresses = ipProps.UnicastAddresses
                    .Where(ua => ua.Address.AddressFamily == AddressFamily.InterNetworkV6)
                    .Select(ua => ua.Address.ToString())
                    .ToList();

                adapter.Gateway = ipProps.GatewayAddresses
                    .Select(g => g.Address.ToString())
                    .Where(g => !g.StartsWith("::") && g != "0.0.0.0")
                    .ToList();

                adapter.DnsServers = ipProps.DnsAddresses
                    .Select(d => d.ToString())
                    .ToList();

                if (ipProps.UnicastAddresses.Any(ua => ua.Address.AddressFamily == AddressFamily.InterNetwork))
                {
                    var ipv4 = ipProps.UnicastAddresses.First(ua => ua.Address.AddressFamily == AddressFamily.InterNetwork);
                    adapter.SubnetMask = ipv4.IPv4Mask?.ToString() ?? "N/A";
                }

                adapter.DhcpEnabled = ipProps.GetIPv4Properties()?.IsDhcpEnabled ?? false;

                adapters.Add(adapter);
            }

            info.Adapters = adapters;

            try
            {
                var publicIP = await GetPublicIPAddressAsync();
                info.PublicIPAddress = publicIP;
            }
            catch
            {
                info.PublicIPAddress = "Unable to retrieve";
            }
        }
        catch (Exception ex)
        {
            info.ErrorMessage = ex.Message;
        }

        return info;
    }

    private static string FormatSpeed(long speed)
    {
        if (speed >= 1_000_000_000)
            return $"{speed / 1_000_000_000.0:F1} Gbps";
        if (speed >= 1_000_000)
            return $"{speed / 1_000_000.0:F0} Mbps";
        if (speed >= 1_000)
            return $"{speed / 1_000.0:F0} Kbps";
        return $"{speed} bps";
    }

    private static async Task<string> GetPublicIPAddressAsync()
    {
        using var client = new HttpClient { Timeout = TimeSpan.FromSeconds(5) };

        var services = new[]
        {
            "https://api.ipify.org",
            "https://icanhazip.com",
            "https://checkip.amazonaws.com",
            "https://ipinfo.io/ip"
        };

        foreach (var service in services)
        {
            try
            {
                var response = await client.GetStringAsync(service);
                return response.Trim();
            }
            catch
            {
                continue;
            }
        }

        throw new Exception("Could not retrieve public IP");
    }
}

public sealed class NetworkInformation
{
    public string HostName { get; set; } = string.Empty;
    public List<string> LocalIPv4Addresses { get; set; } = new();
    public List<string> LocalIPv6Addresses { get; set; } = new();
    public string PublicIPAddress { get; set; } = string.Empty;
    public List<NetworkAdapterInfo> Adapters { get; set; } = new();
    public string ErrorMessage { get; set; } = string.Empty;
}

public sealed class NetworkAdapterInfo
{
    public string Name { get; set; } = string.Empty;
    public string Description { get; set; } = string.Empty;
    public string Status { get; set; } = string.Empty;
    public string Speed { get; set; } = string.Empty;
    public string Type { get; set; } = string.Empty;
    public string MacAddress { get; set; } = string.Empty;
    public List<string> IPv4Addresses { get; set; } = new();
    public List<string> IPv6Addresses { get; set; } = new();
    public List<string> Gateway { get; set; } = new();
    public List<string> DnsServers { get; set; } = new();
    public string SubnetMask { get; set; } = string.Empty;
    public bool DhcpEnabled { get; set; }
}

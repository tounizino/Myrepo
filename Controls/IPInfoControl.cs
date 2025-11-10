using System;
using System.Drawing;
using System.Linq;
using System.Threading.Tasks;
using System.Windows.Forms;
using NetworkToolPro.Services;

namespace NetworkToolPro
{
    public class IPInfoControl : UserControl
    {
        private readonly IPInfoService ipInfoService;
        private ListView adapterListView;
        private TextBox publicIpTextBox;
        private TextBox hostTextBox;
        private Button refreshButton;
        private Label statusLabel;

        public IPInfoControl()
        {
            ipInfoService = new IPInfoService();
            InitializeUI();
            _ = LoadNetworkInfoAsync();
        }

        private void InitializeUI()
        {
            this.BackColor = Color.White;
            this.Padding = new Padding(20);

            var titleLabel = new Label
            {
                Text = "Local Network Interfaces",
                Location = new Point(20, 20),
                Size = new Size(300, 25),
                Font = new Font("Segoe UI", 12, FontStyle.Bold)
            };
            this.Controls.Add(titleLabel);

            refreshButton = new Button
            {
                Text = "Refresh",
                Location = new Point(650, 20),
                Size = new Size(120, 28),
                BackColor = Color.FromArgb(0, 122, 204),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            refreshButton.FlatAppearance.BorderSize = 0;
            refreshButton.Click += async (s, e) => await LoadNetworkInfoAsync();
            this.Controls.Add(refreshButton);

            hostTextBox = new TextBox
            {
                Location = new Point(20, 50),
                Size = new Size(280, 23),
                ReadOnly = true,
                BorderStyle = BorderStyle.None,
                Font = new Font("Segoe UI", 9, FontStyle.Regular)
            };
            this.Controls.Add(hostTextBox);

            publicIpTextBox = new TextBox
            {
                Location = new Point(320, 50),
                Size = new Size(300, 23),
                ReadOnly = true,
                BorderStyle = BorderStyle.None,
                Font = new Font("Segoe UI", 9, FontStyle.Regular)
            };
            this.Controls.Add(publicIpTextBox);

            adapterListView = new ListView
            {
                Location = new Point(20, 80),
                Size = new Size(750, 350),
                View = View.Details,
                FullRowSelect = true,
                GridLines = true,
                Font = new Font("Consolas", 9)
            };
            adapterListView.Columns.Add("Adapter", 150);
            adapterListView.Columns.Add("Description", 180);
            adapterListView.Columns.Add("IPv4", 140);
            adapterListView.Columns.Add("IPv6", 140);
            adapterListView.Columns.Add("Gateway", 120);
            adapterListView.Columns.Add("DNS", 140);
            adapterListView.Columns.Add("DHCP", 60);
            this.Controls.Add(adapterListView);

            statusLabel = new Label
            {
                Text = "Gathering network information...",
                Location = new Point(20, 440),
                Size = new Size(600, 23),
                Font = new Font("Segoe UI", 9, FontStyle.Italic)
            };
            this.Controls.Add(statusLabel);
        }

        private async Task LoadNetworkInfoAsync()
        {
            try
            {
                refreshButton.Enabled = false;
                statusLabel.Text = "Gathering network information...";
                adapterListView.Items.Clear();

                var info = await ipInfoService.GetNetworkInformationAsync();

                if (!string.IsNullOrWhiteSpace(info.ErrorMessage))
                {
                    statusLabel.Text = info.ErrorMessage;
                    return;
                }

                hostTextBox.Text = $"Host: {info.HostName}";
                publicIpTextBox.Text = $"Public IP: {info.PublicIPAddress}";

                foreach (var adapter in info.Adapters)
                {
                    var item = new ListViewItem(adapter.Name);
                    item.SubItems.Add(adapter.Description);
                    item.SubItems.Add(string.Join(", ", adapter.IPv4Addresses));
                    item.SubItems.Add(string.Join(", ", adapter.IPv6Addresses));
                    item.SubItems.Add(string.Join(", ", adapter.Gateway));
                    item.SubItems.Add(string.Join(", ", adapter.DnsServers));
                    item.SubItems.Add(adapter.DhcpEnabled ? "Yes" : "No");

                    adapterListView.Items.Add(item);
                }

                statusLabel.Text = $"Adapters found: {info.Adapters.Count}";
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"Error: {ex.Message}";
            }
            finally
            {
                refreshButton.Enabled = true;
            }
        }
    }
}

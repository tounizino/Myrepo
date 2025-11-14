using System;
using System.Drawing;
using System.Linq;
using System.Net.NetworkInformation;
using System.Threading.Tasks;
using System.Windows.Forms;
using NetworkToolPro.Services;
using NetworkToolPro.UI;

namespace NetworkToolPro
{
    public class IPInfoControl : UserControl
    {
        private readonly IPInfoService ipInfoService;
        private Panel summaryPanel;
        private Panel adaptersPanel;
        private Panel detailsPanel;
        private ListView adapterListView;
        private ListView detailListView;
        private Label hostLabel;
        private Label publicIpLabel;
        private Label adapterCountLabel;
        private Label statusLabel;
        private Button refreshButton;

        public IPInfoControl()
        {
            ipInfoService = new IPInfoService();
            this.Dock = DockStyle.Fill;
            this.AutoScroll = true;
            InitializeUI();
            _ = LoadNetworkInfoAsync();
        }

        private void InitializeUI()
        {
            this.BackColor = ThemeManager.BackgroundPrimary;
            this.Padding = new Padding(20);

            summaryPanel = CreateSummaryPanel();
            summaryPanel.Location = new Point(20, 20);
            summaryPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;
            this.Controls.Add(summaryPanel);

            adaptersPanel = CreateAdaptersPanel();
            adaptersPanel.Location = new Point(20, summaryPanel.Bottom + 15);
            adaptersPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;
            this.Controls.Add(adaptersPanel);

            detailsPanel = CreateDetailsPanel();
            detailsPanel.Location = new Point(20, adaptersPanel.Bottom + 15);
            detailsPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Bottom;
            this.Controls.Add(detailsPanel);

            this.SizeChanged += (s, e) => UpdateLayoutSizes();
            UpdateLayoutSizes();
        }

        private void UpdateLayoutSizes()
        {
            int width = Math.Max(600, this.ClientSize.Width - 40);
            summaryPanel.Size = new Size(width, 180);
            adaptersPanel.Size = new Size(width, 280);
            detailsPanel.Size = new Size(width, Math.Max(150, this.ClientSize.Height - 515));
        }

        private Panel CreateSummaryPanel()
        {
            var panel = new Panel
            {
                Size = new Size(1040, 180),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "📡 Network Snapshot",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(20, 15),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            var hostCard = CreateSummaryCard(panel, "💻 Hostname", "Loading...", new Point(20, 60), CopyHostToClipboard);
            hostLabel = hostCard.ValueLabel;

            var publicCard = CreateSummaryCard(panel, "🌐 Public IP", "Loading...", new Point(380, 60), CopyPublicIpToClipboard);
            publicIpLabel = publicCard.ValueLabel;

            var adapterCard = CreateSummaryCard(panel, "🧩 Active Adapters", "0", new Point(740, 60));
            adapterCountLabel = adapterCard.ValueLabel;

            refreshButton = new Button
            {
                Text = "🔄 Refresh",
                Size = new Size(120, 36),
                Location = new Point(panel.Width - 160, panel.Height - 60),
                Anchor = AnchorStyles.Right | AnchorStyles.Bottom,
                FlatStyle = FlatStyle.Flat,
                BackColor = ThemeManager.AccentPrimary,
                ForeColor = Color.White,
                Font = new Font("Segoe UI", 10, FontStyle.Bold),
                Cursor = Cursors.Hand
            };
            refreshButton.FlatAppearance.BorderSize = 0;
            refreshButton.Click += async (s, e) => await LoadNetworkInfoAsync();
            panel.Controls.Add(refreshButton);

            return panel;
        }

        private (Panel Card, Label ValueLabel) CreateSummaryCard(Panel parent, string title, string value, Point location, EventHandler? copyClick = null)
        {
            var card = new Panel
            {
                Location = location,
                Size = new Size(340, 90),
                BackColor = Color.FromArgb(249, 250, 252),
                Padding = new Padding(15)
            };

            var titleLabel = new Label
            {
                Text = title,
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextMuted,
                Location = new Point(10, 8),
                AutoSize = true
            };
            card.Controls.Add(titleLabel);

            var valueLabel = new Label
            {
                Text = value,
                Font = new Font("Segoe UI", 18, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(8, 30),
                AutoSize = true
            };
            card.Controls.Add(valueLabel);

            if (copyClick != null)
            {
                var copyButton = new Button
                {
                    Text = "📋 Copy",
                    Size = new Size(80, 30),
                    Location = new Point(250, 55),
                    FlatStyle = FlatStyle.Flat,
                    BackColor = Color.FromArgb(241, 245, 249),
                    ForeColor = ThemeManager.TextSecondary,
                    Font = new Font("Segoe UI", 9, FontStyle.Bold),
                    Cursor = Cursors.Hand
                };
                copyButton.FlatAppearance.BorderSize = 0;
                copyButton.Click += copyClick;
                card.Controls.Add(copyButton);
            }

            parent.Controls.Add(card);
            return (card, valueLabel);
        }

        private Panel CreateAdaptersPanel()
        {
            var panel = new Panel
            {
                Size = new Size(1040, 280),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "🧩 Active Network Adapters",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(20, 15),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            adapterListView = new ListView
            {
                Location = new Point(20, 60),
                Size = new Size(panel.Width - 40, panel.Height - 80),
                View = View.Details,
                FullRowSelect = true,
                GridLines = true,
                HideSelection = false,
                Font = new Font("Segoe UI", 9),
                BorderStyle = BorderStyle.FixedSingle,
                Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Bottom
            };
            adapterListView.Columns.Add("Adapter", 160);
            adapterListView.Columns.Add("Status", 100);
            adapterListView.Columns.Add("Type", 120);
            adapterListView.Columns.Add("Speed", 120);
            adapterListView.Columns.Add("IPv4", 150);
            adapterListView.Columns.Add("Gateway", 150);
            adapterListView.Columns.Add("DNS", 200);
            adapterListView.SelectedIndexChanged += AdapterListView_SelectedIndexChanged;

            panel.Controls.Add(adapterListView);

            return panel;
        }

        private Panel CreateDetailsPanel()
        {
            var panel = new Panel
            {
                Size = new Size(1040, 280),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "🔍 Adapter Details",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(20, 15),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            detailListView = new ListView
            {
                Location = new Point(20, 60),
                Size = new Size(panel.Width - 40, panel.Height - 80),
                View = View.Details,
                FullRowSelect = true,
                GridLines = true,
                Font = new Font("Segoe UI", 9),
                BorderStyle = BorderStyle.FixedSingle,
                Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Bottom
            };
            detailListView.Columns.Add("Property", 200);
            detailListView.Columns.Add("Value", 400);
            panel.Controls.Add(detailListView);

            statusLabel = new Label
            {
                Text = "Gathering network information...",
                ForeColor = ThemeManager.TextMuted,
                Font = new Font("Segoe UI", 9, FontStyle.Italic),
                AutoSize = false,
                Size = new Size(panel.Width - 40, 20),
                Location = new Point(20, panel.Height - 30),
                TextAlign = ContentAlignment.MiddleLeft,
                Anchor = AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Bottom
            };
            panel.Controls.Add(statusLabel);

            return panel;
        }

        private async Task LoadNetworkInfoAsync()
        {
            try
            {
                refreshButton.Enabled = false;
                statusLabel.Text = "Refreshing network information...";
                statusLabel.ForeColor = ThemeManager.TextMuted;
                adapterListView.Items.Clear();
                detailListView.Items.Clear();

                var info = await ipInfoService.GetNetworkInformationAsync();

                if (!string.IsNullOrWhiteSpace(info.ErrorMessage))
                {
                    statusLabel.Text = info.ErrorMessage;
                    statusLabel.ForeColor = ThemeManager.AccentDanger;
                    return;
                }

                hostLabel.Text = info.HostName;
                publicIpLabel.Text = string.IsNullOrWhiteSpace(info.PublicIPAddress)
                    ? "Unavailable"
                    : info.PublicIPAddress;
                adapterCountLabel.Text = info.Adapters.Count.ToString();

                foreach (var adapter in info.Adapters)
                {
                    var item = new ListViewItem(adapter.Name);
                    item.SubItems.Add(adapter.Status);
                    item.SubItems.Add(adapter.Type);
                    item.SubItems.Add(adapter.Speed);
                    item.SubItems.Add(string.Join(", ", adapter.IPv4Addresses));
                    item.SubItems.Add(string.Join(", ", adapter.Gateway));
                    item.SubItems.Add(string.Join(", ", adapter.DnsServers));
                    item.Tag = adapter;
                    adapterListView.Items.Add(item);
                }

                if (adapterListView.Items.Count > 0)
                {
                    adapterListView.Items[0].Selected = true;
                }

                statusLabel.Text = $"Adapters found: {info.Adapters.Count}";
                statusLabel.ForeColor = ThemeManager.AccentSuccess;
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"Error: {ex.Message}";
                statusLabel.ForeColor = ThemeManager.AccentDanger;
            }
            finally
            {
                refreshButton.Enabled = true;
            }
        }

        private void AdapterListView_SelectedIndexChanged(object? sender, EventArgs e)
        {
            detailListView.Items.Clear();

            if (adapterListView.SelectedItems.Count == 0)
            {
                return;
            }

            if (adapterListView.SelectedItems[0].Tag is NetworkAdapterInfo adapter)
            {
                AddDetail("Adapter Name", adapter.Name);
                AddDetail("Description", adapter.Description);
                AddDetail("Status", adapter.Status);
                AddDetail("Type", adapter.Type);
                AddDetail("Speed", adapter.Speed);
                AddDetail("MAC Address", adapter.MacAddress);
                AddDetail("DHCP Enabled", adapter.DhcpEnabled ? "Yes" : "No");
                AddDetail("IPv4 Addresses", string.Join("\n", adapter.IPv4Addresses));
                AddDetail("IPv6 Addresses", string.Join("\n", adapter.IPv6Addresses));
                AddDetail("Gateway", string.Join("\n", adapter.Gateway));
                AddDetail("DNS Servers", string.Join("\n", adapter.DnsServers));
                AddDetail("Subnet Mask", adapter.SubnetMask);
            }
        }

        private void AddDetail(string property, string value)
        {
            var item = new ListViewItem(property);
            item.SubItems.Add(string.IsNullOrWhiteSpace(value) ? "N/A" : value);
            detailListView.Items.Add(item);
        }

        private void CopyPublicIpToClipboard(object? sender, EventArgs e)
        {
            if (!string.IsNullOrWhiteSpace(publicIpLabel.Text) && publicIpLabel.Text != "Unavailable")
            {
                Clipboard.SetText(publicIpLabel.Text);
                statusLabel.Text = "Public IP copied to clipboard";
                statusLabel.ForeColor = ThemeManager.AccentSuccess;
            }
        }

        private void CopyHostToClipboard(object? sender, EventArgs e)
        {
            if (!string.IsNullOrWhiteSpace(hostLabel.Text))
            {
                Clipboard.SetText(hostLabel.Text);
                statusLabel.Text = "Hostname copied to clipboard";
                statusLabel.ForeColor = ThemeManager.AccentSuccess;
            }
        }
    }
}

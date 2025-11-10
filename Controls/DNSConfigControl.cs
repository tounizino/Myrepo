using System;
using System.Collections.Generic;
using System.Drawing;
using System.Linq;
using System.Net;
using System.Threading.Tasks;
using System.Windows.Forms;
using NetworkToolPro.Services;

namespace NetworkToolPro
{
    public class DNSConfigControl : UserControl
    {
        private readonly DnsConfigurator dnsConfigurator;
        private ComboBox adapterComboBox;
        private TextBox primaryDnsTextBox;
        private TextBox secondaryDnsTextBox;
        private Button applyButton;
        private Button clearButton;
        private Button refreshButton;
        private ListView currentDnsListView;
        private Label statusLabel;
        private Panel presetsPanel;

        public DNSConfigControl()
        {
            dnsConfigurator = new DnsConfigurator();
            InitializeUI();
            LoadAdapters();
        }

        private void InitializeUI()
        {
            this.BackColor = Color.White;
            this.Padding = new Padding(20);

            var titleLabel = new Label
            {
                Text = "DNS Configuration Manager",
                Location = new Point(20, 20),
                Size = new Size(300, 25),
                Font = new Font("Segoe UI", 12, FontStyle.Bold)
            };
            this.Controls.Add(titleLabel);

            var adapterLabel = new Label
            {
                Text = "Select Network Adapter:",
                Location = new Point(20, 60),
                Size = new Size(200, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            this.Controls.Add(adapterLabel);

            adapterComboBox = new ComboBox
            {
                Location = new Point(20, 85),
                Size = new Size(550, 25),
                DropDownStyle = ComboBoxStyle.DropDownList,
                Font = new Font("Segoe UI", 9)
            };
            adapterComboBox.SelectedIndexChanged += (s, e) => UpdateCurrentDns();
            this.Controls.Add(adapterComboBox);

            refreshButton = new Button
            {
                Text = "Refresh",
                Location = new Point(580, 83),
                Size = new Size(90, 28),
                BackColor = Color.FromArgb(108, 117, 125),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9)
            };
            refreshButton.FlatAppearance.BorderSize = 0;
            refreshButton.Click += (s, e) => LoadAdapters();
            this.Controls.Add(refreshButton);

            var currentDnsLabel = new Label
            {
                Text = "Current DNS Servers:",
                Location = new Point(20, 125),
                Size = new Size(200, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            this.Controls.Add(currentDnsLabel);

            currentDnsListView = new ListView
            {
                Location = new Point(20, 150),
                Size = new Size(650, 80),
                View = View.Details,
                FullRowSelect = true,
                GridLines = true,
                Font = new Font("Consolas", 9)
            };
            currentDnsListView.Columns.Add("Priority", 80);
            currentDnsListView.Columns.Add("DNS Server", 200);
            this.Controls.Add(currentDnsListView);

            var newDnsLabel = new Label
            {
                Text = "Set Custom DNS Servers:",
                Location = new Point(20, 245),
                Size = new Size(200, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            this.Controls.Add(newDnsLabel);

            var primaryLabel = new Label
            {
                Text = "Primary DNS:",
                Location = new Point(20, 275),
                Size = new Size(100, 20)
            };
            this.Controls.Add(primaryLabel);

            primaryDnsTextBox = new TextBox
            {
                Location = new Point(120, 272),
                Size = new Size(200, 25),
                Font = new Font("Segoe UI", 9)
            };
            this.Controls.Add(primaryDnsTextBox);

            var secondaryLabel = new Label
            {
                Text = "Secondary DNS:",
                Location = new Point(340, 275),
                Size = new Size(100, 20)
            };
            this.Controls.Add(secondaryLabel);

            secondaryDnsTextBox = new TextBox
            {
                Location = new Point(450, 272),
                Size = new Size(200, 25),
                Font = new Font("Segoe UI", 9)
            };
            this.Controls.Add(secondaryDnsTextBox);

            applyButton = new Button
            {
                Text = "Apply DNS Settings",
                Location = new Point(20, 310),
                Size = new Size(150, 32),
                BackColor = Color.FromArgb(40, 167, 69),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            applyButton.FlatAppearance.BorderSize = 0;
            applyButton.Click += async (s, e) => await ApplyDnsAsync();
            this.Controls.Add(applyButton);

            clearButton = new Button
            {
                Text = "Reset to DHCP",
                Location = new Point(180, 310),
                Size = new Size(150, 32),
                BackColor = Color.FromArgb(220, 53, 69),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            clearButton.FlatAppearance.BorderSize = 0;
            clearButton.Click += async (s, e) => await ResetToDhcpAsync();
            this.Controls.Add(clearButton);

            presetsPanel = new Panel
            {
                Location = new Point(20, 360),
                Size = new Size(650, 70),
                BorderStyle = BorderStyle.FixedSingle
            };

            var presetsLabel = new Label
            {
                Text = "Quick Presets:",
                Location = new Point(5, 5),
                Size = new Size(100, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold)
            };
            presetsPanel.Controls.Add(presetsLabel);

            AddPresetButton("Google DNS", "8.8.8.8", "8.8.4.4", 5, 30);
            AddPresetButton("Cloudflare", "1.1.1.1", "1.0.0.1", 115, 30);
            AddPresetButton("OpenDNS", "208.67.222.222", "208.67.220.220", 225, 30);
            AddPresetButton("Quad9", "9.9.9.9", "149.112.112.112", 335, 30);
            AddPresetButton("AdGuard", "94.140.14.14", "94.140.15.15", 445, 30);

            this.Controls.Add(presetsPanel);

            statusLabel = new Label
            {
                Text = "⚠ Administrator privileges required to change DNS settings",
                Location = new Point(20, 440),
                Size = new Size(650, 23),
                Font = new Font("Segoe UI", 9, FontStyle.Italic),
                ForeColor = Color.FromArgb(255, 193, 7)
            };
            this.Controls.Add(statusLabel);
        }

        private void AddPresetButton(string name, string primary, string secondary, int x, int y)
        {
            var button = new Button
            {
                Text = name,
                Location = new Point(x, y),
                Size = new Size(100, 28),
                BackColor = Color.FromArgb(0, 122, 204),
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 8)
            };
            button.FlatAppearance.BorderSize = 0;
            button.Click += (s, e) =>
            {
                primaryDnsTextBox.Text = primary;
                secondaryDnsTextBox.Text = secondary;
            };
            presetsPanel.Controls.Add(button);
        }

        private void LoadAdapters(int? interfaceIndexToSelect = null)
        {
            try
            {
                statusLabel.Text = "Loading adapters...";

                var currentSelection = interfaceIndexToSelect ?? (adapterComboBox.SelectedItem as DnsAdapterInfo)?.InterfaceIndex;

                adapterComboBox.BeginUpdate();
                adapterComboBox.Items.Clear();

                var adapters = dnsConfigurator.GetConfigurableAdapters();
                DnsAdapterInfo? adapterToSelect = null;

                foreach (var adapter in adapters)
                {
                    adapterComboBox.Items.Add(adapter);
                    if (currentSelection.HasValue && adapter.InterfaceIndex == currentSelection.Value)
                    {
                        adapterToSelect = adapter;
                    }
                }

                adapterComboBox.EndUpdate();

                if (adapterToSelect != null)
                {
                    adapterComboBox.SelectedItem = adapterToSelect;
                }
                else if (adapterComboBox.Items.Count > 0)
                {
                    adapterComboBox.SelectedIndex = 0;
                }

                UpdateCurrentDns();
                statusLabel.Text = $"Found {adapters.Count} network adapter(s)";
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"Error: {ex.Message}";
                MessageBox.Show($"Error loading adapters: {ex.Message}\n\nMake sure you are running as Administrator.",
                    "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        private void UpdateCurrentDns()
        {
            currentDnsListView.Items.Clear();

            if (adapterComboBox.SelectedItem is DnsAdapterInfo adapter)
            {
                if (adapter.DnsServers.Count > 0)
                {
                    for (int i = 0; i < adapter.DnsServers.Count; i++)
                    {
                        var item = new ListViewItem(i == 0 ? "Primary" : i == 1 ? "Secondary" : $"#{i + 1}");
                        item.SubItems.Add(adapter.DnsServers[i]);
                        currentDnsListView.Items.Add(item);
                    }
                }
                else
                {
                    var item = new ListViewItem("N/A");
                    item.SubItems.Add("Using DHCP (automatic)");
                    currentDnsListView.Items.Add(item);
                }
            }
        }

        private async Task ApplyDnsAsync()
        {
            if (adapterComboBox.SelectedItem is not DnsAdapterInfo adapter)
            {
                MessageBox.Show("Please select a network adapter.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var primary = primaryDnsTextBox.Text.Trim();
            if (string.IsNullOrWhiteSpace(primary))
            {
                MessageBox.Show("Please enter at least a primary DNS server.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (!IPAddress.TryParse(primary, out _))
            {
                MessageBox.Show("Primary DNS must be a valid IPv4 or IPv6 address.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var dnsServers = new List<string> { primary };
            var secondary = secondaryDnsTextBox.Text.Trim();
            if (!string.IsNullOrWhiteSpace(secondary))
            {
                if (!IPAddress.TryParse(secondary, out _))
                {
                    MessageBox.Show("Secondary DNS must be a valid IPv4 or IPv6 address.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    return;
                }

                dnsServers.Add(secondary);
            }

            try
            {
                applyButton.Enabled = false;
                statusLabel.Text = "Applying DNS settings...";

                var result = await dnsConfigurator.SetDnsServersAsync(adapter, dnsServers);

                if (result.IsSuccess)
                {
                    MessageBox.Show($"DNS settings applied successfully!\n\nAdapter: {adapter.Caption}\nPrimary: {primary}\nSecondary: {secondary}",
                        "Success", MessageBoxButtons.OK, MessageBoxIcon.Information);
                    LoadAdapters(adapter.InterfaceIndex);
                }
                else
                {
                    MessageBox.Show($"Failed to apply DNS settings.\n\n{result.Message}",
                        "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }

                statusLabel.Text = result.Message;
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"Error: {ex.Message}";
                MessageBox.Show($"Error applying DNS: {ex.Message}\n\nMake sure you are running as Administrator.",
                    "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            finally
            {
                applyButton.Enabled = true;
            }
        }

        private async Task ResetToDhcpAsync()
        {
            if (adapterComboBox.SelectedItem is not DnsAdapterInfo adapter)
            {
                MessageBox.Show("Please select a network adapter.", "Validation", MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var confirm = MessageBox.Show($"Reset DNS to automatic (DHCP) for:\n{adapter.Caption}?",
                "Confirm", MessageBoxButtons.YesNo, MessageBoxIcon.Question);

            if (confirm != DialogResult.Yes)
                return;

            try
            {
                clearButton.Enabled = false;
                statusLabel.Text = "Resetting DNS to DHCP...";

                var result = await dnsConfigurator.SetDnsServersAsync(adapter, Array.Empty<string>());

                if (result.IsSuccess)
                {
                    MessageBox.Show("DNS settings reset to automatic (DHCP).",
                        "Success", MessageBoxButtons.OK, MessageBoxIcon.Information);
                    LoadAdapters(adapter.InterfaceIndex);
                }
                else
                {
                    MessageBox.Show($"Failed to reset DNS settings.\n\n{result.Message}",
                        "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }

                statusLabel.Text = result.Message;
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"Error: {ex.Message}";
                MessageBox.Show($"Error resetting DNS: {ex.Message}",
                    "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
            finally
            {
                clearButton.Enabled = true;
            }
        }
    }
}

using System;
using System.Collections.Generic;
using System.Drawing;
using System.Drawing.Drawing2D;
using System.Linq;
using System.Net;
using System.Threading.Tasks;
using System.Windows.Forms;
using NetworkToolPro.Services;
using NetworkToolPro.UI;

namespace NetworkToolPro
{
    public class DNSConfigControl : UserControl
    {
        private readonly DnsConfigurator dnsConfigurator;
        private Panel selectionPanel;
        private Panel presetsPanel;
        private Panel manualPanel;
        private Panel currentPanel;
        private ComboBox adapterComboBox;
        private TextBox primaryDnsTextBox;
        private TextBox secondaryDnsTextBox;
        private Button applyButton;
        private Button clearButton;
        private Button refreshButton;
        private ListView currentDnsListView;
        private Label statusLabel;

        public DNSConfigControl()
        {
            dnsConfigurator = new DnsConfigurator();
            this.Dock = DockStyle.Fill;
            this.AutoScroll = true;
            InitializeUI();
            LoadAdapters();
        }

        private void InitializeUI()
        {
            this.BackColor = ThemeManager.BackgroundPrimary;
            this.Padding = new Padding(20);

            selectionPanel = CreateSelectionPanel();
            selectionPanel.Location = new Point(20, 20);
            selectionPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;
            this.Controls.Add(selectionPanel);

            currentPanel = CreateCurrentDnsPanel();
            currentPanel.Location = new Point(20, selectionPanel.Bottom + 15);
            currentPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;
            this.Controls.Add(currentPanel);

            presetsPanel = CreatePresetsPanel();
            presetsPanel.Location = new Point(20, currentPanel.Bottom + 15);
            presetsPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right;
            this.Controls.Add(presetsPanel);

            manualPanel = CreateManualPanel();
            manualPanel.Location = new Point(20, presetsPanel.Bottom + 15);
            manualPanel.Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Bottom;
            this.Controls.Add(manualPanel);
        }

        private Panel CreateSelectionPanel()
        {
            var panel = new Panel
            {
                Size = new Size(this.Width - 40, 140),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "🌐 Adapter Selection",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(20, 15),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            var adapterLabel = new Label
            {
                Text = "Select Network Adapter:",
                Location = new Point(20, 55),
                Size = new Size(200, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextSecondary
            };
            panel.Controls.Add(adapterLabel);

            adapterComboBox = new ComboBox
            {
                Location = new Point(20, 80),
                Size = new Size(panel.Width - 170, 28),
                DropDownStyle = ComboBoxStyle.DropDownList,
                Font = new Font("Segoe UI", 10),
                Anchor = AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Top
            };
            adapterComboBox.SelectedIndexChanged += (s, e) => UpdateCurrentDns();
            panel.Controls.Add(adapterComboBox);

            refreshButton = new Button
            {
                Text = "🔄 Refresh",
                Location = new Point(panel.Width - 130, 78),
                Size = new Size(110, 32),
                BackColor = Color.FromArgb(241, 245, 249),
                ForeColor = ThemeManager.TextSecondary,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                Anchor = AnchorStyles.Top | AnchorStyles.Right,
                Cursor = Cursors.Hand
            };
            refreshButton.FlatAppearance.BorderSize = 0;
            refreshButton.Click += (s, e) => LoadAdapters();
            panel.Controls.Add(refreshButton);

            return panel;
        }

        private Panel CreateCurrentDnsPanel()
        {
            var panel = new Panel
            {
                Size = new Size(this.Width - 40, 180),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "📋 Current DNS Configuration",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(20, 15),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            currentDnsListView = new ListView
            {
                Location = new Point(20, 60),
                Size = new Size(panel.Width - 40, panel.Height - 80),
                View = View.Details,
                FullRowSelect = true,
                GridLines = true,
                Font = new Font("Consolas", 9),
                BorderStyle = BorderStyle.FixedSingle,
                Anchor = AnchorStyles.Top | AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Bottom
            };
            currentDnsListView.Columns.Add("Priority", 120);
            currentDnsListView.Columns.Add("DNS Server Address", 300);
            panel.Controls.Add(currentDnsListView);

            return panel;
        }

        private Panel CreatePresetsPanel()
        {
            var panel = new Panel
            {
                Size = new Size(this.Width - 40, 220),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "⚡ Quick DNS Presets",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(20, 15),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            var y = 60;
            AddPresetCard(panel, "🌍 Google DNS", "8.8.8.8 | 8.8.4.4",  
                "Fast, reliable, global coverage", "8.8.8.8", "8.8.4.4", 20, y);
            
            AddPresetCard(panel, "☁️ Cloudflare", "1.1.1.1 | 1.0.0.1",
                "Privacy-focused, very fast", "1.1.1.1", "1.0.0.1", 270, y);
            
            AddPresetCard(panel, "🔒 OpenDNS", "208.67.222.222 | 208.67.220.220",
                "Family filtering, security", "208.67.222.222", "208.67.220.220", 520, y);

            y = 130;
            AddPresetCard(panel, "🛡️ Quad9", "9.9.9.9 | 149.112.112.112",
                "Blocks malicious domains", "9.9.9.9", "149.112.112.112", 20, y);
            
            AddPresetCard(panel, "🚫 AdGuard", "94.140.14.14 | 94.140.15.15",
                "Blocks ads and trackers", "94.140.14.14", "94.140.15.15", 270, y);

            return panel;
        }

        private void AddPresetCard(Panel parent, string name, string servers, string description, 
            string primary, string secondary, int x, int y)
        {
            var card = new Panel
            {
                Location = new Point(x, y),
                Size = new Size(230, 60),
                BackColor = Color.FromArgb(249, 250, 252),
                Cursor = Cursors.Hand,
                Tag = new { Primary = primary, Secondary = secondary }
            };

            card.Paint += (s, e) =>
            {
                e.Graphics.SmoothingMode = SmoothingMode.AntiAlias;
                using var pen = new Pen(ThemeManager.BorderLight);
                e.Graphics.DrawRectangle(pen, 0, 0, card.Width - 1, card.Height - 1);
            };

            card.MouseEnter += (s, e) => card.BackColor = Color.FromArgb(239, 246, 255);
            card.MouseLeave += (s, e) => card.BackColor = Color.FromArgb(249, 250, 252);
            card.Click += (s, e) =>
            {
                var data = (dynamic)card.Tag;
                primaryDnsTextBox.Text = data.Primary;
                secondaryDnsTextBox.Text = data.Secondary;
                statusLabel.Text = $"Preset selected: {name}";
                statusLabel.ForeColor = ThemeManager.AccentPrimary;
            };

            var nameLabel = new Label
            {
                Text = name,
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(8, 6),
                AutoSize = true
            };
            nameLabel.Click += card.Click;
            card.Controls.Add(nameLabel);

            var serverLabel = new Label
            {
                Text = servers,
                Font = new Font("Segoe UI", 8),
                ForeColor = ThemeManager.AccentPrimary,
                Location = new Point(8, 24),
                AutoSize = true
            };
            serverLabel.Click += card.Click;
            card.Controls.Add(serverLabel);

            var descLabel = new Label
            {
                Text = description,
                Font = new Font("Segoe UI", 7),
                ForeColor = ThemeManager.TextMuted,
                Location = new Point(8, 40),
                AutoSize = true
            };
            descLabel.Click += card.Click;
            card.Controls.Add(descLabel);

            parent.Controls.Add(card);
        }

        private Panel CreateManualPanel()
        {
            var panel = new Panel
            {
                Size = new Size(this.Width - 40, 200),
                BackColor = ThemeManager.BackgroundSecondary,
                Padding = new Padding(20)
            };
            ThemeManager.ApplyCardStyle(panel);

            var titleLabel = new Label
            {
                Text = "✏️ Manual Configuration",
                Font = new Font("Segoe UI", 14, FontStyle.Bold),
                ForeColor = ThemeManager.TextPrimary,
                Location = new Point(20, 15),
                AutoSize = true
            };
            panel.Controls.Add(titleLabel);

            var primaryLabel = new Label
            {
                Text = "Primary DNS Server:",
                Location = new Point(20, 60),
                Size = new Size(150, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextSecondary
            };
            panel.Controls.Add(primaryLabel);

            primaryDnsTextBox = new TextBox
            {
                Location = new Point(20, 85),
                Size = new Size(300, 28),
                Font = new Font("Segoe UI", 10),
                BorderStyle = BorderStyle.FixedSingle
            };
            panel.Controls.Add(primaryDnsTextBox);

            var secondaryLabel = new Label
            {
                Text = "Secondary DNS Server (Optional):",
                Location = new Point(350, 60),
                Size = new Size(220, 20),
                Font = new Font("Segoe UI", 9, FontStyle.Bold),
                ForeColor = ThemeManager.TextSecondary
            };
            panel.Controls.Add(secondaryLabel);

            secondaryDnsTextBox = new TextBox
            {
                Location = new Point(350, 85),
                Size = new Size(300, 28),
                Font = new Font("Segoe UI", 10),
                BorderStyle = BorderStyle.FixedSingle
            };
            panel.Controls.Add(secondaryDnsTextBox);

            applyButton = new Button
            {
                Text = "✓ Apply DNS Settings",
                Location = new Point(20, 135),
                Size = new Size(200, 40),
                BackColor = ThemeManager.AccentSuccess,
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                Cursor = Cursors.Hand
            };
            applyButton.FlatAppearance.BorderSize = 0;
            applyButton.Click += async (s, e) => await ApplyDnsAsync();
            panel.Controls.Add(applyButton);

            clearButton = new Button
            {
                Text = "↺ Reset to DHCP",
                Location = new Point(240, 135),
                Size = new Size(180, 40),
                BackColor = ThemeManager.AccentDanger,
                ForeColor = Color.White,
                FlatStyle = FlatStyle.Flat,
                Font = new Font("Segoe UI", 11, FontStyle.Bold),
                Cursor = Cursors.Hand
            };
            clearButton.FlatAppearance.BorderSize = 0;
            clearButton.Click += async (s, e) => await ResetToDhcpAsync();
            panel.Controls.Add(clearButton);

            statusLabel = new Label
            {
                Text = "⚠ Administrator privileges required to change DNS settings",
                Location = new Point(450, 140),
                Size = new Size(panel.Width - 470, 35),
                Font = new Font("Segoe UI", 9, FontStyle.Italic),
                ForeColor = ThemeManager.AccentWarning,
                Anchor = AnchorStyles.Left | AnchorStyles.Right | AnchorStyles.Top
            };
            panel.Controls.Add(statusLabel);

            return panel;
        }

        private void LoadAdapters(int? interfaceIndexToSelect = null)
        {
            try
            {
                statusLabel.Text = "Loading adapters...";
                statusLabel.ForeColor = ThemeManager.TextMuted;

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
                statusLabel.ForeColor = ThemeManager.AccentSuccess;
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"Error: {ex.Message}";
                statusLabel.ForeColor = ThemeManager.AccentDanger;
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
                    var item = new ListViewItem("Automatic");
                    item.SubItems.Add("Using DHCP (automatic configuration)");
                    item.ForeColor = ThemeManager.TextMuted;
                    currentDnsListView.Items.Add(item);
                }
            }
        }

        private async Task ApplyDnsAsync()
        {
            if (adapterComboBox.SelectedItem is not DnsAdapterInfo adapter)
            {
                MessageBox.Show("Please select a network adapter.", "Validation", 
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var primary = primaryDnsTextBox.Text.Trim();
            if (string.IsNullOrWhiteSpace(primary))
            {
                MessageBox.Show("Please enter at least a primary DNS server.", "Validation", 
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            if (!IPAddress.TryParse(primary, out _))
            {
                MessageBox.Show("Primary DNS must be a valid IPv4 or IPv6 address.", "Validation", 
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
                return;
            }

            var dnsServers = new List<string> { primary };
            var secondary = secondaryDnsTextBox.Text.Trim();
            if (!string.IsNullOrWhiteSpace(secondary))
            {
                if (!IPAddress.TryParse(secondary, out _))
                {
                    MessageBox.Show("Secondary DNS must be a valid IPv4 or IPv6 address.", "Validation", 
                        MessageBoxButtons.OK, MessageBoxIcon.Warning);
                    return;
                }

                dnsServers.Add(secondary);
            }

            try
            {
                applyButton.Enabled = false;
                statusLabel.Text = "Applying DNS settings...";
                statusLabel.ForeColor = ThemeManager.TextMuted;

                var result = await dnsConfigurator.SetDnsServersAsync(adapter, dnsServers);

                if (result.IsSuccess)
                {
                    statusLabel.Text = "✓ DNS settings applied successfully!";
                    statusLabel.ForeColor = ThemeManager.AccentSuccess;
                    MessageBox.Show($"✓ DNS settings applied successfully!\n\n" +
                        $"Adapter: {adapter.Caption}\n" +
                        $"Primary DNS: {primary}\n" +
                        $"Secondary DNS: {(string.IsNullOrWhiteSpace(secondary) ? "None" : secondary)}",
                        "Success", MessageBoxButtons.OK, MessageBoxIcon.Information);
                    LoadAdapters(adapter.InterfaceIndex);
                }
                else
                {
                    statusLabel.Text = $"✗ Failed: {result.Message}";
                    statusLabel.ForeColor = ThemeManager.AccentDanger;
                    MessageBox.Show($"Failed to apply DNS settings.\n\n{result.Message}",
                        "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"✗ Error: {ex.Message}";
                statusLabel.ForeColor = ThemeManager.AccentDanger;
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
                MessageBox.Show("Please select a network adapter.", "Validation", 
                    MessageBoxButtons.OK, MessageBoxIcon.Warning);
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
                statusLabel.ForeColor = ThemeManager.TextMuted;

                var result = await dnsConfigurator.SetDnsServersAsync(adapter, Array.Empty<string>());

                if (result.IsSuccess)
                {
                    statusLabel.Text = "✓ DNS reset to automatic (DHCP)";
                    statusLabel.ForeColor = ThemeManager.AccentSuccess;
                    MessageBox.Show("DNS settings reset to automatic (DHCP).",
                        "Success", MessageBoxButtons.OK, MessageBoxIcon.Information);
                    LoadAdapters(adapter.InterfaceIndex);
                }
                else
                {
                    statusLabel.Text = $"✗ Failed: {result.Message}";
                    statusLabel.ForeColor = ThemeManager.AccentDanger;
                    MessageBox.Show($"Failed to reset DNS settings.\n\n{result.Message}",
                        "Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
                }
            }
            catch (Exception ex)
            {
                statusLabel.Text = $"✗ Error: {ex.Message}";
                statusLabel.ForeColor = ThemeManager.AccentDanger;
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

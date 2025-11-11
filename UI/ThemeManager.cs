using System.Drawing;
using System.Drawing.Drawing2D;
using System.Windows.Forms;

namespace NetworkToolPro.UI;

public static class ThemeManager
{
    public static Color BackgroundPrimary => Color.FromArgb(248, 250, 252);
    public static Color BackgroundSecondary => Color.White;
    public static Color AccentPrimary => Color.FromArgb(99, 102, 241);
    public static Color AccentSecondary => Color.FromArgb(59, 130, 246);
    public static Color AccentSuccess => Color.FromArgb(16, 185, 129);
    public static Color AccentDanger => Color.FromArgb(239, 68, 68);
    public static Color AccentWarning => Color.FromArgb(250, 204, 21);
    public static Color TextPrimary => Color.FromArgb(15, 23, 42);
    public static Color TextSecondary => Color.FromArgb(71, 85, 105);
    public static Color TextMuted => Color.FromArgb(148, 163, 184);
    public static Color BorderLight => Color.FromArgb(226, 232, 240);

    public static void ApplyCardStyle(Control control)
    {
        control.BackColor = BackgroundSecondary;
        control.ForeColor = TextPrimary;
        control.Padding = new Padding(18);
        control.Paint += (s, e) =>
        {
            e.Graphics.SmoothingMode = SmoothingMode.AntiAlias;
            using var borderPen = new Pen(BorderLight);
            var rect = control.ClientRectangle;
            rect.Width -= 1;
            rect.Height -= 1;
            e.Graphics.DrawRoundedRectangle(borderPen, rect, 12);
        };
    }

    public static Panel CreateGradientPanel(Color start, Color end)
    {
        var panel = new Panel
        {
            DoubleBuffered = true
        };

        panel.Paint += (s, e) =>
        {
            using var brush = new LinearGradientBrush(panel.ClientRectangle, start, end, 45f);
            e.Graphics.FillRectangle(brush, panel.ClientRectangle);
        };

        return panel;
    }

    public static void EnableDoubleBuffering(Control control)
    {
        const int doubleBufferFlag = 0x02000000;
        control.GetType().GetProperty("DoubleBuffered", System.Reflection.BindingFlags.NonPublic | System.Reflection.BindingFlags.Instance)?.SetValue(control, true, null);
        control.SetStyle(ControlStyles.OptimizedDoubleBuffer | ControlStyles.UserPaint | ControlStyles.AllPaintingInWmPaint, true);
        control.UpdateStyles();
        control.CreateParams.ExStyle |= doubleBufferFlag;
    }

    public static Label CreateLabel(string text, Font font, Color color, ContentAlignment alignment = ContentAlignment.MiddleLeft)
    {
        return new Label
        {
            Text = text,
            Font = font,
            ForeColor = color,
            AutoSize = false,
            TextAlign = alignment
        };
    }
}

internal static class GraphicsExtensions
{
    public static void DrawRoundedRectangle(this Graphics graphics, Pen pen, Rectangle bounds, int radius)
    {
        int diameter = radius * 2;
        Size size = new Size(diameter, diameter);
        Rectangle arc = new Rectangle(bounds.Location, size);

        // top left arc
        graphics.DrawArc(pen, arc, 180, 90);

        // top right arc
        arc.X = bounds.Right - diameter;
        graphics.DrawArc(pen, arc, 270, 90);

        // bottom right arc
        arc.Y = bounds.Bottom - diameter;
        graphics.DrawArc(pen, arc, 0, 90);

        // bottom left arc
        arc.X = bounds.Left;
        graphics.DrawArc(pen, arc, 90, 90);

        graphics.DrawLine(pen, bounds.Left + radius, bounds.Top, bounds.Right - radius, bounds.Top);
        graphics.DrawLine(pen, bounds.Right, bounds.Top + radius, bounds.Right, bounds.Bottom - radius);
        graphics.DrawLine(pen, bounds.Left + radius, bounds.Bottom, bounds.Right - radius, bounds.Bottom);
        graphics.DrawLine(pen, bounds.Left, bounds.Top + radius, bounds.Left, bounds.Bottom - radius);
    }
}

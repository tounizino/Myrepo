# WordPress Responsive Table Templates - Light Theme

Ready-to-use, WordPress-friendly HTML table templates with inline CSS. No plugins required!

## 📋 Features

✅ **WordPress Compatible** - Works with Classic Editor and Gutenberg (HTML block)  
✅ **Fully Responsive** - Adapts beautifully to all screen sizes  
✅ **Mobile Friendly** - Stacks vertically on mobile devices  
✅ **Light Theme** - Clean, professional appearance  
✅ **No Shadows or Glowing** - Simple, flat design  
✅ **Copy & Paste** - Just customize your content and go!  
✅ **No External Dependencies** - All styles are inline  

---

## 📁 Available Templates

### 1. **wordpress-responsive-table.html**
**Best for:** General purpose tables with 4+ columns  
**Features:** 
- Full-featured responsive design
- Hover effects on rows
- Alternating row colors
- Mobile-optimized label system

### 2. **wordpress-table-simple.html**
**Best for:** Simple tables with 2-3 columns  
**Features:**
- Minimal, clean design
- Lightweight code
- Perfect for basic data presentation

### 3. **wordpress-table-pricing.html**
**Best for:** Pricing tables, feature comparisons, product specs  
**Features:**
- Centered content (except first column)
- Perfect for checkmarks and comparison data
- Highlighted feature names

---

## 🚀 How to Use

### Step 1: Choose Your Template
Pick the template that best fits your needs from the files above.

### Step 2: Copy the Code
Open the HTML file and copy the entire code (both HTML and CSS).

### Step 3: Paste into WordPress

#### For Classic Editor:
1. Switch to the **Text** tab (not Visual)
2. Paste the code where you want the table
3. Edit the content between the tags

#### For Gutenberg/Block Editor:
1. Add a **Custom HTML** block
2. Paste the code into the block
3. Preview to see the table
4. Edit the content as needed

### Step 4: Customize Your Content
Replace the placeholder text with your actual data:
- Change `<th>Header 1</th>` to your column names
- Update `<td>Data 1</td>` with your cell content
- Add or remove rows as needed

---

## 📱 Responsive Behavior

### Desktop (768px and above)
- Full table layout
- All columns visible side by side
- Hover effects on rows

### Mobile (below 768px)
- Table stacks vertically
- Each row becomes a card
- Column labels appear before each cell
- Easy to scroll and read

---

## ✏️ Customization Tips

### Adding More Rows
Copy an existing `<tr>` block and paste it in the `<tbody>` section:

```html
<tr>
  <td data-label="Header 1">Your Data</td>
  <td data-label="Header 2">Your Data</td>
  <td data-label="Header 3">Your Data</td>
</tr>
```

### Adding More Columns
1. Add a new `<th>` in the header row
2. Add a new `<td>` in each data row
3. Update the `data-label` attribute to match the header

### Removing Columns
1. Delete the corresponding `<th>` from the header
2. Delete the corresponding `<td>` from all rows

### Changing Colors
Modify these values in the `<style>` section:

```css
background-color: #f8f9fa;  /* Header background */
color: #2c3e50;             /* Header text */
border: 1px solid #e0e0e0;  /* Border color */
```

### Adjusting Table Width
The table is set to 100% width by default. To make it narrower:

```css
.wp-responsive-table-wrapper {
  width: 80%;  /* Change from 100% */
  margin: 20px auto;  /* Center it */
}
```

---

## 🎨 Design Specifications

**Typography:**
- Font: System fonts (Apple, Segoe UI, Roboto)
- Header: 12px uppercase with letter spacing
- Body: 14px with 1.6 line height

**Colors:**
- Background: #ffffff (white)
- Header BG: #f8f9fa (light gray)
- Text: #333333 (dark gray)
- Borders: #e0e0e0 (light gray)

**Spacing:**
- Cell padding: 12-14px
- Table margin: 20px vertical
- Mobile padding: 8-10px

---

## 🔧 Troubleshooting

### Table Doesn't Display
- Make sure you're in the HTML/Text editor, not Visual
- Check that all opening tags have closing tags
- Verify the code wasn't auto-formatted by WordPress

### Styles Not Working
- Ensure the `<style>` tag is included with the table
- If using Gutenberg, make sure you're using the Custom HTML block
- Some themes might override styles - increase specificity if needed

### Mobile View Issues
- The `data-label` attributes must match your header text
- Test on actual mobile devices or use browser dev tools
- Clear cache if changes don't appear

### Spacing Issues
- WordPress might add extra `<p>` tags - remove them in HTML view
- Use the Custom HTML block in Gutenberg to avoid auto-formatting

---

## 💡 Pro Tips

1. **Keep it Simple:** Don't add too many columns (4-6 max for readability)
2. **Test Mobile First:** Always check how it looks on mobile devices
3. **Use Consistent Data:** Keep data formats consistent within columns
4. **Accessible Content:** Use clear, descriptive headers
5. **Save Templates:** Save your customized tables in a document for reuse

---

## 🆘 Quick Reference

### Basic Table Structure
```html
<div class="wrapper">
  <table class="table-name">
    <thead>
      <tr>
        <th>Header</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td data-label="Header">Data</td>
      </tr>
    </tbody>
  </table>
</div>
<style>
  /* CSS styles here */
</style>
```

### Essential Parts
- `<thead>` = Table header section
- `<tbody>` = Table body (data rows)
- `<th>` = Header cell
- `<td>` = Data cell
- `data-label` = Mobile label (must match header)

---

## 📝 Examples

### Example 1: Product Comparison
```html
<th>Product</th>
<th>Price</th>
<th>Rating</th>

<td data-label="Product">Widget Pro</td>
<td data-label="Price">$49.99</td>
<td data-label="Rating">4.5/5</td>
```

### Example 2: Schedule Table
```html
<th>Time</th>
<th>Monday</th>
<th>Tuesday</th>

<td data-label="Time">9:00 AM</td>
<td data-label="Monday">Meeting</td>
<td data-label="Tuesday">Workshop</td>
```

### Example 3: Statistics
```html
<th>Metric</th>
<th>Value</th>
<th>Change</th>

<td data-label="Metric">Visitors</td>
<td data-label="Value">1,234</td>
<td data-label="Change">+12%</td>
```

---

## 🎯 Best Practices

1. **Always include `data-label` attributes** for mobile responsiveness
2. **Match data-label text to header text** for consistency
3. **Test in preview mode** before publishing
4. **Keep cell content concise** for better mobile display
5. **Use semantic HTML** (thead, tbody structure)
6. **Maintain equal columns per row** for proper formatting

---

## 📄 License

These templates are free to use for personal and commercial projects.  
No attribution required. Customize as needed!

---

## 🤝 Need Help?

- Check your WordPress theme documentation
- Test in a different browser
- Disable plugins temporarily to check for conflicts
- Use browser developer tools to inspect the table

---

**Happy Blogging! 📝**

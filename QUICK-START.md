# Quick Start Guide

## 🚀 Get Your Table in 3 Steps

### Step 1: Choose Your Template
- **Standard Table** → `wordpress-responsive-table.html` (4+ columns)
- **Simple Table** → `wordpress-table-simple.html` (2-3 columns)
- **Pricing Table** → `wordpress-table-pricing.html` (comparisons)

### Step 2: Copy the Code
Open the HTML file and copy **everything** (HTML + CSS).

### Step 3: Paste into WordPress

#### Gutenberg Editor:
1. Click `+` to add a block
2. Search for "Custom HTML"
3. Paste the code
4. Edit your content

#### Classic Editor:
1. Switch to "Text" tab
2. Paste the code
3. Edit your content
4. Preview before publishing

---

## ✏️ Edit Your Content

Replace placeholder text:

```html
<!-- Change headers -->
<th>Your Header</th>

<!-- Change data -->
<td data-label="Your Header">Your Data</td>
```

**Important:** Keep `data-label` matching your header text!

---

## 📱 Test Mobile View

- Use browser dev tools (F12 → mobile view)
- Or publish and test on real device
- Tables automatically stack on screens < 768px

---

## 🎨 Common Customizations

### Change Header Color
```css
.wp-responsive-table thead {
  background-color: #your-color;
}
```

### Change Border Color
```css
border: 1px solid #your-color;
```

### Adjust Table Width
```css
.wp-responsive-table-wrapper {
  width: 90%;  /* Instead of 100% */
  margin: 20px auto;  /* Center it */
}
```

---

## 🔧 Troubleshooting

**Table not showing?**
→ Make sure you're in HTML/Text mode, not Visual

**Styles not working?**
→ Check that the `<style>` tag is included

**Mobile view broken?**
→ Verify all `data-label` attributes are present

**WordPress adds weird spacing?**
→ Use Custom HTML block in Gutenberg

---

## 💡 Pro Tips

1. **Preview First** - Always preview before publishing
2. **Keep It Simple** - Max 6 columns for best readability
3. **Test Mobile** - Always check mobile view
4. **Save Templates** - Keep customized versions in a doc
5. **Use Consistent Data** - Keep formats uniform in each column

---

## 📋 Need More Help?

Check `TABLE-USAGE-GUIDE.md` for complete documentation!

---

**You're ready to go! 🎉**

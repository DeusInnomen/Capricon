using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Globalization;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace ArtShow
{
    public partial class FrmEditShopItem : Form
    {
        public PrintShopItem ShopItem { get; set; }

        public FrmEditShopItem(PrintShopItem item, int artistAttendingID = -1)
        {
            InitializeComponent();
            TxtNotes.SetWatermark("Optional");
            TxtPrice.SetWatermark("NFS If Blank");

            ShopItem = item ?? new PrintShopItem();
            if (artistAttendingID > -1) ShopItem.ArtistAttendingID = artistAttendingID;
            if (item == null) return;

            LblShowNumber.Text = ShopItem.ShowNumber != null ? ShopItem.ShowNumber.ToString() : "TBD";
            TxtTitle.Text = ShopItem.Title;
            TxtMedia.Text = ShopItem.Media;
            NumQuantitySent.Value = ShopItem.QuantitySent;
            TxtPrice.Text = ShopItem.Price.ToString();
            TxtNotes.Text = ShopItem.Notes ?? "";
            TxtLocation.Text = ShopItem.LocationCode ?? "";
            CmbCategory.SelectedIndex = ShopItem.Category != null ? CmbCategory.FindString(ShopItem.Category) : 0;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void BtnSave_Click(object sender, EventArgs e)
        {
            TxtPrice.BackColor = SystemColors.Control;
            decimal price;
            if (TxtPrice.Text == "" || !decimal.TryParse(TxtPrice.Text, NumberStyles.Currency, null, out price))
            {
                TxtPrice.BackColor = Color.Yellow;
                TxtPrice.Focus();
                return;
            }
            ShopItem.Title = TxtTitle.Text;
            ShopItem.Media = TxtMedia.Text;
            ShopItem.QuantitySent = Convert.ToInt32(NumQuantitySent.Value);
            ShopItem.Price = price;
            ShopItem.Notes = TxtNotes.Text.Trim().Length > 0 ? TxtNotes.Text : null;
            ShopItem.LocationCode = TxtLocation.Text.Trim().Length > 0 ? TxtLocation.Text : null;
            ShopItem.Category = CmbCategory.SelectedItem != null ? CmbCategory.SelectedItem.ToString() : null;

            if (ShopItem.Save())
                DialogResult = DialogResult.OK;
            else
                MessageBox.Show("An error occurred trying to save this item: " + ShopItem.LastError, "Error",
                                MessageBoxButtons.OK, MessageBoxIcon.Error);

        }
    }
}

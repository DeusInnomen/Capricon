using System;
using System.Drawing;
using System.Globalization;
using System.Windows.Forms;

namespace ArtShow
{
    public partial class FrmEditShowItem : Form
    {
        public ArtShowItem ShowItem { get; set; }

        public FrmEditShowItem(ArtShowItem item, int artistAttendingID = -1)
        {
            InitializeComponent();
            TxtNotes.SetWatermark("Optional");
            TxtBid.SetWatermark("NFS If Blank");
            TxtPrintMax.SetWatermark("Optional");

            ShowItem = item ?? new ArtShowItem();
            if (artistAttendingID > -1) ShowItem.ArtistAttendingID = artistAttendingID;
            if (item == null) return;

            LblShowNumber.Text = ShowItem.ShowNumber != null ? ShowItem.ShowNumber.ToString() : "TBD";
            TxtTitle.Text = ShowItem.Title;
            ChkOriginal.Checked = ShowItem.IsOriginal;
            TxtMedia.Text = ShowItem.Media;
            TxtPrintNum.Text = ShowItem.PrintNumber ?? "";
            TxtPrintMax.Text = ShowItem.PrintMaxNumber ?? "";
            TxtBid.Text = ShowItem.MinimumBid != null ? ShowItem.MinimumBid.ToString() : "";
            TxtNotes.Text = ShowItem.Notes ?? "";
            TxtLocation.Text = ShowItem.LocationCode ?? "";
            CmbCategory.SelectedIndex = ShowItem.Category != null ? CmbCategory.FindString(ShowItem.Category) : 0;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void BtnSave_Click(object sender, EventArgs e)
        {
            TxtBid.BackColor = SystemColors.Control;
            decimal price = 0;
            if (TxtBid.Text != "" && !decimal.TryParse(TxtBid.Text, NumberStyles.Currency, null, out price))
            {
                TxtBid.BackColor = Color.Yellow;
                TxtBid.Focus();
                return;
            }
            ShowItem.Title = TxtTitle.Text;
            ShowItem.IsOriginal = ChkOriginal.Checked;
            ShowItem.Media = TxtMedia.Text;
            ShowItem.PrintNumber = TxtPrintNum.Text.Trim().Length > 0 ? TxtPrintNum.Text : null;
            ShowItem.PrintMaxNumber = TxtPrintMax.Text.Trim().Length > 0 ? TxtPrintMax.Text : null;
            ShowItem.MinimumBid = TxtBid.Text.Trim().Length > 0 ? price : (decimal?)null;
            ShowItem.Notes = TxtNotes.Text.Trim().Length > 0 ? TxtNotes.Text : null;
            ShowItem.LocationCode = TxtLocation.Text.Trim().Length > 0 ? TxtLocation.Text : null;
            ShowItem.Category = CmbCategory.SelectedItem != null ? CmbCategory.SelectedItem.ToString() : null;

            if (ShowItem.Save())
                DialogResult = DialogResult.OK;
            else
                MessageBox.Show("An error occurred trying to save this item: " + ShowItem.LastError, "Error",
                                MessageBoxButtons.OK, MessageBoxIcon.Error);
        }
    }
}

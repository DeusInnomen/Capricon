using System;
using System.Windows.Forms;

namespace Registration
{
    public partial class FrmMiscCharge : Form
    {
        public string ChargeDescription { get { return TxtDescription.Text; } }
        public decimal ChargeAmount { get { return Convert.ToDecimal(TxtCharge.Text); } }

        public FrmMiscCharge()
        {
            InitializeComponent();
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }

        private void BtnOK_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.OK;
        }
    }
}

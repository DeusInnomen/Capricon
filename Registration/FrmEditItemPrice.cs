using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace Registration
{
    public partial class FrmEditItemPrice : Form
    {
        public decimal NewAmount { get { return Convert.ToDecimal(TxtCharge.Text); } }
        
        public FrmEditItemPrice(decimal currentPrice)
        {
            InitializeComponent();
            TxtCharge.Text = currentPrice.ToString();
        }

        private void BtnOK_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.OK;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            DialogResult = DialogResult.Cancel;
        }
    }
}

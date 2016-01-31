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
    public partial class FrmGetDetail : Form
    {
        public string DetailValue
        {
            get { return TxtValue.Text; }
            set { TxtValue.Text = value; }
        }

        public FrmGetDetail(string request)
        {
            InitializeComponent();
            LblRequest.Text = request;
            TxtValue.Focus();
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

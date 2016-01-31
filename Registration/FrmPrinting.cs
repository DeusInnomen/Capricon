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
    public partial class FrmPrinting : Form
    {
        private int MaxLabels { get; set; }
        private bool Cancel { get; set; }

        public FrmPrinting(int totalLabels)
        {
            InitializeComponent();
            MaxLabels = totalLabels;
            Cancel = false;
        }

        public bool SetDisplay(int labelNumber, string details)
        {
            LblPrintNumber.Text = labelNumber + " of " + MaxLabels;
            LblDetails.Text = details;
            return !Cancel;
        }

        private void BtnCancel_Click(object sender, EventArgs e)
        {
            Cancel = true;
        }
    }
}

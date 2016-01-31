using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading;
using System.Windows.Forms;

namespace ArtShow
{
    public partial class FrmCaptureCard : Form
    {
        public MagneticStripeScan Card { get; private set; }

        private string ScannedText { get; set; }
        private bool IsManual { get; set; }

        public FrmCaptureCard()
        {
            InitializeComponent();
            Card = null;
            ScannedText = string.Empty;
            IsManual = false;
            this.Height = 112;
            TxtCCNumber.Tag = string.Empty;

            CmbCCMonth.SelectedIndex = DateTime.Now.Month - 1;
            for (var mod = 0; mod < 8; mod++)
                CmbCCYear.Items.Add(DateTime.Now.Year + mod);
            CmbCCYear.SelectedIndex = 0;
        }

        private void FrmCaptureCard_KeyPress(object sender, KeyPressEventArgs e)
        {
            if(IsManual)
                ScannedText = string.Empty;
            else
            {
                if (e.KeyChar == 10 || e.KeyChar == 13)
                    ProcessScannedText();
                else
                    ScannedText += (char)e.KeyChar;
                e.Handled = true;                
            }
        }

        private void ProcessScannedText()
        {
            var card = new MagneticStripeScan(ScannedText);
            ScannedText = string.Empty;
            if(card.Valid)
            {
                Card = card;
                DialogResult = DialogResult.OK;
            }
            else
            {
                lblBadRead.Visible = true;
                new Thread(new ThreadStart(() =>
                {
                    Thread.Sleep(1000);
                    lblBadRead.Invoke((MethodInvoker)delegate() { lblBadRead.Visible = false; });
                })).Start();
            }
        }

        private void btnManual_Click(object sender, EventArgs e)
        {
            IsManual = !IsManual;
            this.Height = IsManual ? 214 : 112;
            lblManual.Visible = IsManual;
            lblSwipe.Visible = !IsManual;
        }

        private void btnUseCard_Click(object sender, EventArgs e)
        {
            if (!IsManual) return;            
            Card = new MagneticStripeScan((string)TxtCCNumber.Tag, CmbCCMonth.SelectedItem.ToString(), 
                CmbCCYear.SelectedItem.ToString().Substring(2, 2));
            if (Card.Valid)
                DialogResult = DialogResult.OK;
            else
            {
                TxtCCNumber.Text = "";
                TxtCCNumber.Tag = string.Empty;
                lblBadRead.Visible = true;
                new Thread(new ThreadStart(() =>
                {
                    Thread.Sleep(1000);
                    lblBadRead.Invoke((MethodInvoker)delegate() { lblBadRead.Visible = false; });
                })).Start();
            }
        }

        private void btnCancel_Click(object sender, EventArgs e)
        {
            Card = null;
            DialogResult = DialogResult.Cancel;
        }

        private void TxtCCNumber_KeyPress(object sender, KeyPressEventArgs e)
        {
            if (e.KeyChar == (char)Keys.Back && ((string)TxtCCNumber.Tag).Length > 0)
            {
                TxtCCNumber.Tag = ((string)TxtCCNumber.Tag).Remove(((string)TxtCCNumber.Tag).Length - 1, 1);
                TxtCCNumber.Text = new String('*', ((string)TxtCCNumber.Tag).Length);
            }
            else if(char.IsDigit(e.KeyChar))
            {
                TxtCCNumber.Tag = (string)TxtCCNumber.Tag + (char)e.KeyChar;
                TxtCCNumber.Text = new String('*', ((string)TxtCCNumber.Tag).Length - 1) + (char)e.KeyChar;
            }
            TxtCCNumber.SelectionStart = TxtCCNumber.TextLength;
            TxtCCNumber.SelectionLength = 0;
            btnUseCard.Enabled = (((string)TxtCCNumber.Tag).StartsWith("3") && TxtCCNumber.TextLength == 15) || TxtCCNumber.TextLength == 16;
            e.Handled = true;
        }

        private void TxtCCNumber_Leave(object sender, EventArgs e)
        {
            TxtCCNumber.Text = new String('*', ((string)TxtCCNumber.Tag).Length);
        }
    }
}

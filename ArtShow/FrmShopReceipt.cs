using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using Microsoft.Reporting.WinForms;

namespace ArtShow
{
    public partial class FrmShopReceipt : Form
    {
        private Person Purchaser { get; set; }
        private List<PrintShopItem> Items { get; set; }
        private string Source { get; set; }
        private string Reference { get; set; }
        private decimal Tax { get; set; }
        
        public FrmShopReceipt(Person purchaser, List<PrintShopItem> items, string source, string reference, decimal taxes)
        {
            InitializeComponent();
            Purchaser = purchaser;
            Items = items;
            Source = source;
            Reference = reference;
            Tax = taxes;
        }

        private void FrmShopReceipt_Load(object sender, EventArgs e)
        {
            RptViewer.RenderingComplete += RptViewer_RenderingComplete;
            var year = (Program.Year - 1980).ToString();
            RptViewer.LocalReport.SetParameters(new ReportParameter("Purchaser", Purchaser.Name));
            RptViewer.LocalReport.SetParameters(new ReportParameter("CapriconYear", year));
            RptViewer.LocalReport.SetParameters(new ReportParameter("PaymentSource", Source));
            RptViewer.LocalReport.SetParameters(new ReportParameter("PaymentReference", Reference));
            RptViewer.LocalReport.SetParameters(new ReportParameter("Taxes", Tax.ToString("G")));
            PrintShopItemBindingSource.DataSource = Items;
            RptViewer.RefreshReport();
        }

        void RptViewer_RenderingComplete(object sender, RenderingCompleteEventArgs e)
        {
            RptViewer.PrinterSettings.Copies = 2;
            RptViewer.PrintDialog();
            DialogResult = DialogResult.OK;
        }
    }
}

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
    public partial class FrmArtistCheckoutSheet : Form
    {
        private List<CheckoutItems> Items { get; set; }

        public FrmArtistCheckoutSheet(List<CheckoutItems> items)
        {
            InitializeComponent();
            Items = items;
        }

        private void FrmShowTags_Load(object sender, EventArgs e)
        {
            var year = (Program.Year - 1980).ToString();
            RptViewer.LocalReport.SetParameters(new ReportParameter("CapriconYear", year));
            var ds = new ReportDataSource("CheckoutItem", Items);
            RptViewer.LocalReport.DataSources.Clear();
            RptViewer.LocalReport.DataSources.Add(ds);
            RptViewer.RefreshReport();
            RptViewer.PrinterSettings.Copies = 2;
        }
    }
}

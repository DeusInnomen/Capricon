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
    public partial class FrmAuctionReport : Form
    {
        private List<ArtShowItem> Items { get; set; }

        public FrmAuctionReport(List<ArtShowItem> items)
        {
            InitializeComponent();
            Items = items;
        }

        private void FrmAuctionReport_Load(object sender, EventArgs e)
        {
            var year = (Program.Year - 1980).ToString();
            RptViewer.LocalReport.SetParameters(new ReportParameter("CapriconYear", year));
            ArtShowItemBindingSource.DataSource = Items;
            RptViewer.RefreshReport();
        }
    }
}

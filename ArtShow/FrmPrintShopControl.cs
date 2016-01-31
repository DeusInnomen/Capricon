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
    public partial class FrmPrintShopControl : Form
    {
        private Artist Artist { get; set; }
        private List<PrintShopItem> Items { get; set; }

        public FrmPrintShopControl(Artist artist, List<PrintShopItem> items)
        {
            InitializeComponent();
            Artist = artist;
            Items = items;
        }

        private void FrmShowTags_Load(object sender, EventArgs e)
        {
            var year = (Program.Year - 1980).ToString();
            RptViewer.LocalReport.SetParameters(new ReportParameter("ArtistName", Artist.LegalName));
            RptViewer.LocalReport.SetParameters(new ReportParameter("ArtistNumber", Artist.ArtistNumber.ToString()));
            RptViewer.LocalReport.SetParameters(new ReportParameter("CapriconYear", year));
            PrintShopItemBindingSource.DataSource = Items;
            RptViewer.RefreshReport();
        }
    }
}

using Microsoft.Reporting.WinForms;
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;

namespace ArtShow
{
    public partial class FrmArtistsWithWaivedFees : Form
    {
        private List<ArtistWithWaivedFees> Items { get; set; }

        public FrmArtistsWithWaivedFees(List<ArtistWithWaivedFees> items)
        {
            InitializeComponent();
            Items = items;
        }

        private void FrmArtistsWithWaivedFees_Load(object sender, EventArgs e)
        {
            var year = (Program.Year - 1980).ToString();
            RptViewer.LocalReport.SetParameters(new ReportParameter("CapriconYear", year));
            ArtistWithWaivedFeesBindingSource.DataSource = Items;
            RptViewer.RefreshReport();
        }
    }
}

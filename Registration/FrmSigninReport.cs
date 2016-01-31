using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Windows.Forms;
using Microsoft.Reporting.WinForms;

namespace Registration
{
    public partial class FrmSigninReport : Form
    {
        private List<GeneratedBadge> Items { get; set; }

        public FrmSigninReport(List<GeneratedBadge> badges)
        {
            InitializeComponent();
            Items = badges;
        }

        private void FrmSigninReport_Load(object sender, EventArgs e)
        {
            var year = (DateTime.Now.Year - 1980).ToString();
            RptViewer.LocalReport.SetParameters(new ReportParameter("CapriconYear", year));
            badgesBindingSource.DataSource = Items;
            RptViewer.RefreshReport();
        }
    }
}

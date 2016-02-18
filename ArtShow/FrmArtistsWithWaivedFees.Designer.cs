namespace ArtShow
{
    partial class FrmArtistsWithWaivedFees
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            Microsoft.Reporting.WinForms.ReportDataSource reportDataSource1 = new Microsoft.Reporting.WinForms.ReportDataSource();
            this.ArtistWithWaivedFeesBindingSource = new System.Windows.Forms.BindingSource(this.components);
            this.RptViewer = new Microsoft.Reporting.WinForms.ReportViewer();
            ((System.ComponentModel.ISupportInitialize)(this.ArtistWithWaivedFeesBindingSource)).BeginInit();
            this.SuspendLayout();
            // 
            // ArtistWithWaivedFeesBindingSource
            // 
            this.ArtistWithWaivedFeesBindingSource.DataSource = typeof(ArtShow.ArtistWithWaivedFees);
            // 
            // RptViewer
            // 
            this.RptViewer.Dock = System.Windows.Forms.DockStyle.Fill;
            reportDataSource1.Name = "WaivedFees";
            reportDataSource1.Value = this.ArtistWithWaivedFeesBindingSource;
            this.RptViewer.LocalReport.DataSources.Add(reportDataSource1);
            this.RptViewer.LocalReport.ReportEmbeddedResource = "ArtShow.FeeWaiverReasonsSheet.rdlc";
            this.RptViewer.Location = new System.Drawing.Point(0, 0);
            this.RptViewer.Name = "RptViewer";
            this.RptViewer.ShowBackButton = false;
            this.RptViewer.ShowCredentialPrompts = false;
            this.RptViewer.ShowFindControls = false;
            this.RptViewer.ShowParameterPrompts = false;
            this.RptViewer.ShowRefreshButton = false;
            this.RptViewer.ShowStopButton = false;
            this.RptViewer.Size = new System.Drawing.Size(938, 500);
            this.RptViewer.TabIndex = 1;
            // 
            // FrmArtistsWithWaivedFees
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(938, 500);
            this.Controls.Add(this.RptViewer);
            this.Name = "FrmArtistsWithWaivedFees";
            this.Text = "Artists With Waived Fees";
            this.Load += new System.EventHandler(this.FrmArtistsWithWaivedFees_Load);
            ((System.ComponentModel.ISupportInitialize)(this.ArtistWithWaivedFeesBindingSource)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private Microsoft.Reporting.WinForms.ReportViewer RptViewer;
        private System.Windows.Forms.BindingSource ArtistWithWaivedFeesBindingSource;
    }
}
namespace ArtShow
{
    partial class FrmAuctionSweep
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmAuctionSweep));
            this.ArtShowItemBindingSource = new System.Windows.Forms.BindingSource(this.components);
            this.RptViewer = new Microsoft.Reporting.WinForms.ReportViewer();
            ((System.ComponentModel.ISupportInitialize)(this.ArtShowItemBindingSource)).BeginInit();
            this.SuspendLayout();
            // 
            // ArtShowItemBindingSource
            // 
            this.ArtShowItemBindingSource.DataSource = typeof(ArtShow.ArtShowItem);
            // 
            // RptViewer
            // 
            this.RptViewer.Dock = System.Windows.Forms.DockStyle.Fill;
            reportDataSource1.Name = "ShowItem";
            reportDataSource1.Value = this.ArtShowItemBindingSource;
            this.RptViewer.LocalReport.ReportEmbeddedResource = "ArtShow.AuctionUnsoldInventory.rdlc";
            this.RptViewer.LocalReport.DataSources.Add(reportDataSource1);
            this.RptViewer.Location = new System.Drawing.Point(0, 0);
            this.RptViewer.Name = "RptViewer";
            this.RptViewer.ServerReport.BearerToken = null;
            this.RptViewer.Size = new System.Drawing.Size(783, 454);
            this.RptViewer.TabIndex = 0;
            // 
            // FrmAuctionSweep
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(783, 454);
            this.Controls.Add(this.RptViewer);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Name = "FrmAuctionSweep";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Auction Unsold Inventory Report";
            this.Load += new System.EventHandler(this.FrmAuctionSweep_Load);
            ((System.ComponentModel.ISupportInitialize)(this.ArtShowItemBindingSource)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private Microsoft.Reporting.WinForms.ReportViewer RptViewer;
        private System.Windows.Forms.BindingSource ArtShowItemBindingSource;
    }
}
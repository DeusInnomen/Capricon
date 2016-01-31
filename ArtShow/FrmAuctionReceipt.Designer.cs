namespace ArtShow
{
    partial class FrmAuctionReceipt
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
            this.RptViewer = new Microsoft.Reporting.WinForms.ReportViewer();
            this.artShowItemBindingSource = new System.Windows.Forms.BindingSource(this.components);
            ((System.ComponentModel.ISupportInitialize)(this.artShowItemBindingSource)).BeginInit();
            this.SuspendLayout();
            // 
            // RptViewer
            // 
            this.RptViewer.Dock = System.Windows.Forms.DockStyle.Fill;
            reportDataSource1.Name = "ShowItem";
            reportDataSource1.Value = this.artShowItemBindingSource;
            this.RptViewer.LocalReport.DataSources.Add(reportDataSource1);
            this.RptViewer.LocalReport.ReportEmbeddedResource = "ArtShow.AuctionSalesReceipt.rdlc";
            this.RptViewer.Location = new System.Drawing.Point(0, 0);
            this.RptViewer.Name = "RptViewer";
            this.RptViewer.Size = new System.Drawing.Size(610, 444);
            this.RptViewer.TabIndex = 0;
            // 
            // artShowItemBindingSource
            // 
            this.artShowItemBindingSource.DataSource = typeof(ArtShow.ArtShowItem);
            // 
            // FrmShopReceipt
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(610, 444);
            this.Controls.Add(this.RptViewer);
            this.Name = "FrmShopReceipt";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Auction Sales Receipt";
            this.Load += new System.EventHandler(this.FrmShopReceipt_Load);
            ((System.ComponentModel.ISupportInitialize)(this.artShowItemBindingSource)).EndInit();
            this.ResumeLayout(false);

        }

        #endregion

        private Microsoft.Reporting.WinForms.ReportViewer RptViewer;
        private System.Windows.Forms.BindingSource artShowItemBindingSource;
    }
}
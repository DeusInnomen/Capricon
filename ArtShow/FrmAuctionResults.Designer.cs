namespace ArtShow
{
    partial class FrmAuctionResults
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
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(FrmAuctionResults));
            this.RptViewer = new Microsoft.Reporting.WinForms.ReportViewer();
            this.SuspendLayout();
            // 
            // RptViewer
            // 
            this.RptViewer.Dock = System.Windows.Forms.DockStyle.Fill;
            this.RptViewer.LocalReport.ReportEmbeddedResource = "ArtShow.AuctionResultsSheet.rdlc";
            this.RptViewer.Location = new System.Drawing.Point(0, 0);
            this.RptViewer.Margin = new System.Windows.Forms.Padding(4, 5, 4, 5);
            this.RptViewer.Name = "RptViewer";
            this.RptViewer.ShowBackButton = false;
            this.RptViewer.ShowRefreshButton = false;
            this.RptViewer.ShowStopButton = false;
            this.RptViewer.ShowZoomControl = false;
            this.RptViewer.Size = new System.Drawing.Size(1448, 698);
            this.RptViewer.TabIndex = 0;
            // 
            // FrmAuctionResults
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(9F, 20F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(1448, 698);
            this.Controls.Add(this.RptViewer);
            this.Icon = ((System.Drawing.Icon)(resources.GetObject("$this.Icon")));
            this.Margin = new System.Windows.Forms.Padding(4, 5, 4, 5);
            this.Name = "FrmAuctionResults";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Private Auction Report";
            this.Load += new System.EventHandler(this.FrmAuctionReport_Load);
            this.ResumeLayout(false);

        }

        #endregion

        private Microsoft.Reporting.WinForms.ReportViewer RptViewer;

    }
}
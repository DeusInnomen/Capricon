namespace ArtShow
{
    partial class FrmArtistCheckoutSheet
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
            this.RptViewer = new Microsoft.Reporting.WinForms.ReportViewer();
            this.SuspendLayout();
            // 
            // RptViewer
            // 
            this.RptViewer.Dock = System.Windows.Forms.DockStyle.Fill;
            this.RptViewer.LocalReport.ReportEmbeddedResource = "ArtShow.ArtShowCheckoutSheet.rdlc";
            this.RptViewer.Location = new System.Drawing.Point(0, 0);
            this.RptViewer.Name = "RptViewer";
            this.RptViewer.ShowBackButton = false;
            this.RptViewer.ShowCredentialPrompts = false;
            this.RptViewer.ShowFindControls = false;
            this.RptViewer.ShowParameterPrompts = false;
            this.RptViewer.ShowRefreshButton = false;
            this.RptViewer.ShowStopButton = false;
            this.RptViewer.Size = new System.Drawing.Size(949, 507);
            this.RptViewer.TabIndex = 0;
            // 
            // FrmArtShowControl
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 13F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.ClientSize = new System.Drawing.Size(949, 507);
            this.Controls.Add(this.RptViewer);
            this.Name = "FrmArtShowControl";
            this.Text = "Art Show Control Sheet";
            this.Load += new System.EventHandler(this.FrmShowTags_Load);
            this.ResumeLayout(false);

        }

        #endregion

        private Microsoft.Reporting.WinForms.ReportViewer RptViewer;
    }
}
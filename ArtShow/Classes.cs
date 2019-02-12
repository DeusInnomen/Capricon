using System;
using System.Collections;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Net;
using System.Runtime.InteropServices;
using System.Text;
using System.Web;
using System.Windows.Forms;
using Newtonsoft.Json;

namespace ArtShow
{
    public class BoolConverter : JsonConverter
    {
        public override void WriteJson(JsonWriter writer, object value, JsonSerializer serializer)
        {
            writer.WriteValue(((bool)value) ? 1 : 0);
        }

        public override object ReadJson(JsonReader reader, Type objectType, object existingValue, JsonSerializer serializer)
        {
            return reader.Value != null ? (reader.Value.ToString() == "1") : false;
        }

        public override bool CanConvert(Type objectType)
        {
            return objectType == typeof(bool);
        }
    }

    public class Person
    {
        [JsonProperty("OneTimeID")]
        public int? OneTimeID { get; set; }
        [JsonProperty("PeopleID")]
        public int? PeopleID { get; set; }
        [JsonProperty("FirstName")]
        public string FirstName { get; set; }
        [JsonProperty("LastName")]
        public string LastName { get; set; }
        [JsonProperty("Address1")]
        public string Address1 { get; set; }
        [JsonProperty("Address2")]
        public string Address2 { get; set; }
        [JsonProperty("City")]
        public string City { get; set; }
        [JsonProperty("State")]
        public string State { get; set; }
        [JsonProperty("ZipCode")]
        public string ZipCode { get; set; }
        [JsonProperty("Country")]
        public string Country { get; set; }
        [JsonProperty("Phone1")]
        public string Phone1 { get; set; }
        [JsonProperty("Phone1Type")]
        public string Phone1Type { get; set; }
        [JsonProperty("Phone2")]
        public string Phone2 { get; set; }
        [JsonProperty("Phone2Type")]
        public string Phone2Type { get; set; }
        [JsonProperty("Email")]
        public string Email { get; set; }
        [JsonProperty("Registered")]
        public DateTime Registered { get; set; }
        [JsonProperty("BadgeName")]
        public string BadgeName { get; set; }
        [JsonProperty("BadgeNumber")]
        public int? BadgeNumber { get; set; }
        [JsonProperty("Banned"), JsonConverter(typeof(BoolConverter))]
        public bool Banned { get; set; }
        [JsonProperty("ParentID")]
        public int? ParentID { get; set; }
        [JsonProperty("HeardFrom")]
        public string HeardFrom { get; set; }
        [JsonProperty("LastChanged")]
        public DateTime LastChanged { get; set; }
        [JsonProperty("ParentName")]
        public string ParentName { get; set; }
        [JsonProperty("ParentContact")]
        public string ParentContact { get; set; }
        [JsonProperty("IsCharity"), JsonConverter(typeof(BoolConverter))]
        public bool IsCharity { get; set; }
        [JsonProperty("DisplayName")]
        public string DisplayName { get; set; }

        public string LastError { get; set; }

        public string Name
        {
            get
            {
                var name = "";
                name += (FirstName ?? "") + " ";
                name += LastName ?? "";
                return name.Trim();
            }
        }

        public override string ToString()
        {
            return Name;
        }

        public bool Save(string newPassword = null)
        {
            var payload = "action=";
            if (PeopleID != null)
                payload += "UpdatePerson";
            else
                payload += "SaveNewPerson&password=NotYetActivated";

            payload += "&person=" + HttpUtility.UrlEncode(JsonConvert.SerializeObject(this));
            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var webResponse = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(webResponse.GetResponseStream()).ReadToEnd();
            var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            if (response.ContainsKey("error"))
            {
                LastError = (string)response["error"];
                return false;
            }
            LastError = null;
            if (response.ContainsKey("peopleID"))
                PeopleID = Convert.ToInt32(response["peopleID"]);
            if (response.ContainsKey("oneTimeID"))
                OneTimeID = Convert.ToInt32(response["oneTimeID"]);
            return true;
        }
    }

    public class Artist
    {
        [JsonProperty("ArtistID")]
        public int? ArtistID { get; set; }
        [JsonProperty("PeopleID")]
        public int PeopleID { get; set; }
        [JsonProperty("ArtistNumber")]
        public int ArtistNumber { get; set; }
        [JsonProperty("DisplayName")]
        public string DisplayName { get; set; }
        [JsonProperty("LegalName")]
        public string LegalName { get; set; }
        [JsonProperty("IsPro"), JsonConverter(typeof(BoolConverter))]
        public bool IsPro { get; set; }
        [JsonProperty("IsEAP"), JsonConverter(typeof(BoolConverter))]
        public bool IsEAP { get; set; }
        [JsonProperty("IsCharity"), JsonConverter(typeof(BoolConverter))]
        public bool IsCharity { get; set; }
        [JsonProperty("Website")]
        public string Website { get; set; }
        [JsonProperty("ArtType")]
        public string ArtType { get; set; }
        [JsonProperty("Notes")]
        public string Notes { get; set; }
    }

    public class ArtistPresence
    {
        [JsonProperty("ArtistAttendingID")]
        public int? ArtistAttendingID { get; set; }
        [JsonProperty("ArtistNumber")]
        public int? ArtistNumber { get; set; }
        [JsonProperty("IsAttending"), JsonConverter(typeof(BoolConverter))]
        public bool IsAttending { get; set; }
        [JsonProperty("HasBadge"), JsonConverter(typeof(BoolConverter))]
        public bool HasBadge { get; set; }
        [JsonProperty("AgentName")]
        public string AgentName { get; set; }
        [JsonProperty("AgentContact")]
        public string AgentContact { get; set; }
        [JsonProperty("ShippingPref")]
        public string ShippingPref { get; set; }
        [JsonProperty("ShippingAddress")]
        public string ShippingAddress { get; set; }
        [JsonProperty("ShippingCost")]
        public decimal? ShippingCost { get; set; }
        [JsonProperty("ShippingPrepaid")]
        public decimal? ShippingPrepaid { get; set; }
        [JsonProperty("ShippingDetails")]
        public string ShippingDetails { get; set; }
        [JsonProperty("NeedsElectricity"), JsonConverter(typeof(BoolConverter))]
        public bool NeedsElectricity { get; set; }
        [JsonProperty("NumTables")]
        public int NumTables { get; set; }
        [JsonProperty("NumGrid")]
        public int NumGrid { get; set; }
        [JsonProperty("HasPrintShop"), JsonConverter(typeof(BoolConverter))]
        public bool HasPrintShop { get; set; }
        [JsonProperty("Notes")]
        public string Notes { get; set; }
        [JsonProperty("Status")]
        public string Status { get; set; }
        [JsonProperty("StatusReason")]
        public string StatusReason { get; set; }
        [JsonProperty("LocationCode")]
        public string LocationCode { get; set; }
        [JsonProperty("FeesWaivedReason")]
        public string FeesWaivedReason { get; set; }        
    }

    public class ArtShowItem
    {
        [JsonProperty("ArtID")]
        public int? ArtID { get; set; }
        [JsonProperty("ArtistAttendingID")]
        public int? ArtistAttendingID { get; set; }
        [JsonProperty("ArtistNumber")]
        public int? ArtistNumber { get; set; }
        [JsonProperty("DisplayName")]
        public string ArtistDisplayName { get; set; }
        [JsonProperty("LegalName")]
        public string ArtistLegalName { get; set; }
        [JsonProperty("ShowNumber")]
        public int? ShowNumber { get; set; }
        [JsonProperty("Title")]
        public string Title { get; set; }
        [JsonProperty("Notes")]
        public string Notes { get; set; }
        [JsonProperty("IsOriginal"), JsonConverter(typeof(BoolConverter))]
        public bool IsOriginal { get; set; }
        [JsonProperty("OriginalMedia")]
        public string Media { get; set; }
        [JsonProperty("PrintNumber")]
        public string PrintNumber { get; set; }
        [JsonProperty("PrintMaxNumber")]
        public string PrintMaxNumber { get; set; }
        [JsonProperty("MinimumBid")]
        public decimal? MinimumBid { get; set; }
        [JsonProperty("QuickSalePrice")]
        public decimal? QuickSalePrice { get; set; }
        [JsonProperty("FeesPaid"), JsonConverter(typeof(BoolConverter))]
        public bool FeesPaid { get; set; }
        [JsonProperty("Category")]
        public string Category { get; set; }
        [JsonProperty("LocationCode")]
        public string LocationCode { get; set; }
        [JsonProperty("PurchaserBadgeID")]
        public int? PurchaserID { get; set; }
        [JsonProperty("PurchaserNumber")]
        public int? PurchaserNumber { get; set; }
        [JsonProperty("PurchaserName")]
        public string PurchaserName { get; set; }
        [JsonProperty("FinalSalePrice")]
        public decimal? FinalSalePrice { get; set; }
        [JsonProperty("Auctioned"), JsonConverter(typeof(BoolConverter))]
        public bool Auctioned { get; set; }
        [JsonProperty("CheckedIn"), JsonConverter(typeof(BoolConverter))]
        public bool CheckedIn { get; set; }
        [JsonProperty("Claimed"), JsonConverter(typeof(BoolConverter))]
        public bool Claimed { get; set; }
        [JsonProperty("IsCharity"), JsonConverter(typeof(BoolConverter))]
        public bool IsCharity { get; set; }

        public string LastError { get; set; }

        public string PrintNumberMessage
        {
            get
            {
                if (PrintNumber == null)
                    return "N/A";
                var message = PrintNumber;
                if (PrintMaxNumber != null) message += " of " + PrintMaxNumber;
                return message;
            }
        }

        public bool Sold
        {
            get { return FinalSalePrice != null; }
        }

        public decimal HangingFee
        {
            get
            {
                if (MinimumBid != null && MinimumBid < 100)
                    return (decimal) 0.50;
                return (decimal) 1.00;
            }
        }

        public decimal HangingFeeOwed
        {
            get { return !FeesPaid ? HangingFee : 0; }
        }

        public bool Save()
        {
            if (ArtistAttendingID == null)
            {
                LastError = "ArtistAttendingID required.";
                return false;
            }
            var payload = "action=";
            payload += ArtID != null ? "UpdateShowItem" : "NewShowItem";
            payload += "&id=" + ArtistAttendingID + "&year=" + Program.Year.ToString() + "&item=" + HttpUtility.UrlEncode(JsonConvert.SerializeObject(this));
            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var webResponse = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(webResponse.GetResponseStream()).ReadToEnd();
            var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            if (response.ContainsKey("Message"))
            {
                LastError = (string)response["Message"];
                return false;
            }
            LastError = null;
            if (response.ContainsKey("ArtID"))
                ArtID = Convert.ToInt32(response["ArtID"]);
            if (response.ContainsKey("ShowNumber"))
                ShowNumber = Convert.ToInt32(response["ShowNumber"]);
            return true;
        }
    }

    public class PrintShopItem
    {
        [JsonProperty("ArtID")]
        public int? ArtID { get; set; }
        [JsonProperty("ArtistAttendingID")]
        public int? ArtistAttendingID { get; set; }
        [JsonProperty("ArtistName")]
        public string ArtistName { get; set; }
        [JsonProperty("ShowNumber")]
        public int? ShowNumber { get; set; }
        [JsonProperty("Title")]
        public string Title { get; set; }
        [JsonProperty("Notes")]
        public string Notes { get; set; }
        [JsonProperty("OriginalMedia")]
        public string Media { get; set; }
        [JsonProperty("QuantitySent")]
        public int QuantitySent { get; set; }
        [JsonProperty("QuantitySold")]
        public int QuantitySold { get; set; }
        [JsonProperty("QuickSalePrice")]
        public decimal Price { get; set; }
        [JsonProperty("Category")]
        public string Category { get; set; }
        [JsonProperty("LocationCode")]
        public string LocationCode { get; set; }
        [JsonProperty("CheckedIn"), JsonConverter(typeof(BoolConverter))]
        public bool CheckedIn { get; set; }
        [JsonProperty("Claimed"), JsonConverter(typeof(BoolConverter))]
        public bool Claimed { get; set; }
        [JsonProperty("IsCharity"), JsonConverter(typeof(BoolConverter))]
        public bool IsCharity { get; set; }

        public string LastError { get; set; }

        public bool Save()
        {
            if (ArtistAttendingID == null)
            {
                LastError = "ArtistAttendingID required.";
                return false;
            }
            var payload = "action=";
            payload += ArtID != null ? "UpdateShopItem" : "NewShopItem";
            payload += "&id=" + ArtistAttendingID + "&year=" + Program.Year.ToString() + "&item=" + HttpUtility.UrlEncode(JsonConvert.SerializeObject(this));
            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/artQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var webResponse = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(webResponse.GetResponseStream()).ReadToEnd();
            var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            if (response.ContainsKey("Message"))
            {
                LastError = (string)response["Message"];
                return false;
            }
            LastError = null;
            if (response.ContainsKey("ArtID"))
                ArtID = Convert.ToInt32(response["ArtID"]);
            if (response.ContainsKey("ShowNumber"))
                ShowNumber = Convert.ToInt32(response["ShowNumber"]);
            return true;
        }

        internal PrintShopItem Clone()
        {
            var copy = new PrintShopItem
                {
                    ArtID = ArtID,
                    ArtistAttendingID = ArtistAttendingID,
                    ArtistName = ArtistName,
                    Category = Category,
                    CheckedIn = CheckedIn,
                    LastError = LastError,
                    LocationCode = LocationCode,
                    Media = Media,
                    Notes = Notes,
                    Price = Price,
                    ShowNumber = ShowNumber,
                    Title = Title,
                    QuantitySent = QuantitySent,
                    QuantitySold = QuantitySold,
                    IsCharity = IsCharity
                };
            return copy;
        }
    }

    public class CheckoutItems
    {
        [JsonProperty("ArtID")]
        public int? ArtID { get; set; }
        [JsonProperty("ArtistAttendingID")]
        public int? ArtistAttendingID { get; set; }
        [JsonProperty("ArtistNumber")]
        public int? ArtistNumber { get; set; }
        [JsonProperty("LegalName")]
        public string ArtistLegalName { get; set; }
        [JsonProperty("ShowNumber")]
        public int? ShowNumber { get; set; }
        [JsonProperty("IsPrintShop"), JsonConverter(typeof(BoolConverter))]
        public bool IsPrintShop { get; set; }
        [JsonProperty("Title")]
        public string Title { get; set; }
        [JsonProperty("OriginalMedia")]
        public string Media { get; set; }
        [JsonProperty("PrintNumber")]
        public string PrintNumber { get; set; }
        [JsonProperty("PrintMaxNumber")]
        public string PrintMaxNumber { get; set; }
        [JsonProperty("MinimumBid")]
        public decimal? MinimumBid { get; set; }
        [JsonProperty("QuickSalePrice")]
        public decimal? QuickSalePrice { get; set; }
        [JsonProperty("FeesPaid"), JsonConverter(typeof(BoolConverter))]
        public bool FeesPaid { get; set; }
        [JsonProperty("LocationCode")]
        public string LocationCode { get; set; }
        [JsonProperty("PurchaserBadgeID")]
        public int? PurchaserID { get; set; }
        [JsonProperty("PurchaserNumber")]
        public int? PurchaserNumber { get; set; }
        [JsonProperty("PurchaserName")]
        public string PurchaserName { get; set; }
        [JsonProperty("FinalSalePrice")]
        public decimal? FinalSalePrice { get; set; }
        [JsonProperty("QuantitySent")]
        public int? QuantitySent { get; set; }
        [JsonProperty("QuantitySold")]
        public int? QuantitySold { get; set; }
        [JsonProperty("Auctioned"), JsonConverter(typeof(BoolConverter))]
        public bool Auctioned { get; set; }
        [JsonProperty("CheckedIn"), JsonConverter(typeof(BoolConverter))]
        public bool CheckedIn { get; set; }
        [JsonProperty("Claimed")]
        public int? Claimed { get; set; }
        [JsonProperty("ShippingCost")]
        public decimal? ShippingCost { get; set; }
        [JsonProperty("ShippingPrepaid")]
        public decimal? ShippingPrepaid { get; set; }
        [JsonProperty("ShippingDetails")]
        public string ShippingDetails { get; set; }
        [JsonProperty("IsEAP"), JsonConverter(typeof(BoolConverter))]
        public bool IsEAP { get; set; }
        [JsonProperty("IsCharity"), JsonConverter(typeof(BoolConverter))]
        public bool IsCharity { get; set; }

        public string LastError { get; set; }

        public string PrintNumberMessage
        {
            get
            {
                if (PrintNumber == null)
                    return "N/A";
                var message = PrintNumber;
                if (PrintMaxNumber != null) message += " of " + PrintMaxNumber;
                return message;
            }
        }

        public bool Sold
        {
            get { return FinalSalePrice != null; }
        }

        public decimal ShopTotalSales
        {
            get { return (decimal) (IsPrintShop ? QuantitySold*QuickSalePrice : 0); }
        }

        public decimal TotalRevenue
        {
            get { return IsPrintShop ? ShopTotalSales : (FinalSalePrice ?? 0);}
        }

        public decimal HangingFee
        {
            get
            {
                if (IsPrintShop || IsEAP) return 0;
                if (MinimumBid != null && MinimumBid < 100)
                    return (decimal)0.50;
                return (decimal)1.00;
            }
        }

        public decimal HangingFeeOwed
        {
            get { return !IsPrintShop ? (!FeesPaid && !IsEAP ? HangingFee : 0) : 0; }
        }
    }


    public class ListViewItemComparer : IComparer
    {
        private readonly bool _asc;
        private readonly int _col;

        public ListViewItemComparer(int column, bool ascending)
        {
            _col = column;
            _asc = ascending;
        }

        public int Compare(object x, object y)
        {
            int returnVal;

            int tmpValue;
            if (int.TryParse(((ListViewItem)x).SubItems[_col].Text, out tmpValue) &&
                int.TryParse(((ListViewItem)y).SubItems[_col].Text, out tmpValue))
                returnVal = Convert.ToInt32(((ListViewItem)x).SubItems[_col].Text).CompareTo(
                    Convert.ToInt32(((ListViewItem)y).SubItems[_col].Text));
            else
                returnVal = ((ListViewItem)x).SubItems[_col].Text.CompareTo(
                    ((ListViewItem)y).SubItems[_col].Text);
            return returnVal * (_asc ? 1 : -1);
        }
    }

    public static class StateArray
    {

        private static List<USState> states;

        static StateArray()
        {
            states = new List<USState>(50)
                {
                    new USState("AL", "Alabama"),
                    new USState("AK", "Alaska"),
                    new USState("AZ", "Arizona"),
                    new USState("AR", "Arkansas"),
                    new USState("CA", "California"),
                    new USState("CO", "Colorado"),
                    new USState("CT", "Connecticut"),
                    new USState("DE", "Delaware"),
                    new USState("DC", "District Of Columbia"),
                    new USState("FL", "Florida"),
                    new USState("GA", "Georgia"),
                    new USState("HI", "Hawaii"),
                    new USState("ID", "Idaho"),
                    new USState("IL", "Illinois"),
                    new USState("IN", "Indiana"),
                    new USState("IA", "Iowa"),
                    new USState("KS", "Kansas"),
                    new USState("KY", "Kentucky"),
                    new USState("LA", "Louisiana"),
                    new USState("ME", "Maine"),
                    new USState("MD", "Maryland"),
                    new USState("MA", "Massachusetts"),
                    new USState("MI", "Michigan"),
                    new USState("MN", "Minnesota"),
                    new USState("MS", "Mississippi"),
                    new USState("MO", "Missouri"),
                    new USState("MT", "Montana"),
                    new USState("NE", "Nebraska"),
                    new USState("NV", "Nevada"),
                    new USState("NH", "New Hampshire"),
                    new USState("NJ", "New Jersey"),
                    new USState("NM", "New Mexico"),
                    new USState("NY", "New York"),
                    new USState("NC", "North Carolina"),
                    new USState("ND", "North Dakota"),
                    new USState("OH", "Ohio"),
                    new USState("OK", "Oklahoma"),
                    new USState("OR", "Oregon"),
                    new USState("PA", "Pennsylvania"),
                    new USState("RI", "Rhode Island"),
                    new USState("SC", "South Carolina"),
                    new USState("SD", "South Dakota"),
                    new USState("TN", "Tennessee"),
                    new USState("TX", "Texas"),
                    new USState("UT", "Utah"),
                    new USState("VT", "Vermont"),
                    new USState("VA", "Virginia"),
                    new USState("WA", "Washington"),
                    new USState("WV", "West Virginia"),
                    new USState("WI", "Wisconsin"),
                    new USState("WY", "Wyoming")
                };
        }

        public static string[] Abbreviations()
        {
            var abbrevList = new List<string>(states.Count);
            abbrevList.AddRange(states.Select(state => state.Abbreviation));
            return abbrevList.ToArray();
        }

        public static string[] Names()
        {
            var nameList = new List<string>(states.Count);
            nameList.AddRange(states.Select(state => state.Name));
            return nameList.ToArray();
        }

        public static USState[] States()
        {
            return states.ToArray();
        }

    }

    public class USState
    {

        public USState()
        {
            Name = null;
            Abbreviation = null;
        }

        public USState(string ab, string name)
        {
            Name = name;
            Abbreviation = ab;
        }

        public string Name { get; set; }

        public string Abbreviation { get; set; }

        public override string ToString()
        {
            return !string.IsNullOrEmpty(Name) ? string.Format("{0} - {1}", Abbreviation, Name) : "";
        }
    }

    public static class TextBoxWatermarkExtensionMethod
    {
        private const uint ECM_FIRST = 0x1500;
        private const uint EM_SETCUEBANNER = ECM_FIRST + 1;

        [DllImport("user32.dll", CharSet = CharSet.Auto, SetLastError = false)]
        private static extern IntPtr SendMessage(IntPtr hWnd, uint Msg, uint wParam, [MarshalAs(UnmanagedType.LPWStr)] string lParam);

        public static void SetWatermark(this TextBox textBox, string watermarkText)
        {
            SendMessage(textBox.Handle, EM_SETCUEBANNER, 0, watermarkText);
        }

    }

    public class PersonPickup
    {
        [JsonProperty("PeopleID")]
        public int? PeopleID { get; set; }
        [JsonProperty("OneTimeID")]
        public int? OneTimeID { get; set; }
        [JsonProperty("BadgeID")]
        public int BadgeID { get; set; }
        [JsonProperty("BadgeNumber")]
        public int BadgeNumber { get; set; }
        [JsonProperty("FirstName")]
        public string FirstName { get; set; }
        [JsonProperty("LastName")]
        public string LastName { get; set; }
        [JsonProperty("BadgeName")]
        public string BadgeName { get; set; }
        [JsonProperty("TotalPieces")]
        public int TotalPieces { get; set; }
        [JsonProperty("TotalDue")]
        public decimal TotalDue { get; set; }

        public string Name
        {
            get
            {
                var name = "";
                name += (FirstName ?? "") + " ";
                name += LastName ?? "";
                return name.Trim();
            }
        }
    }

    public class MagneticStripeScan
    {
        public string CardNumber { get; private set; }
        public string ExpireMonth { get; private set; }
        public string ExpireYear { get; private set; }
        public string CardHolder { get; private set; }
        public eCardType CardType { get; private set; }
        public bool Valid { get; private set; }
        public string RawData { get; private set; }

        public MagneticStripeScan(string number, string expireMonth, string expireYear)
        {
            CardNumber = number;
            ExpireMonth = expireMonth;
            ExpireYear = expireYear;
            CardHolder = string.Empty;
            RawData = string.Empty;
            Valid = Validate();
        }

        public MagneticStripeScan(string scannedData)
        {
            RawData = scannedData;

            string track1 = "";
            string track2 = "";
            string track3 = "";
            string track1_cardholder = "";
            string track1_expmo = "";
            string track1_expyr = "";
            string track1_cvv = "";
            string track1_ccn = "";
            string track2_ccn = "";
            string track2_expmo = "";
            string track2_expyr = "";
            string track2_encpin = "";

            bool in_track_1 = true;
            bool in_track_2 = false;
            bool in_track_3 = false;
            bool track1_caret1_found = false;
            bool track1_caret2_found = false;
            bool track2_equals_found = false;

            int track1_leg3_count = 0;
            int track2_leg2_count = 0;

            try
            {
                foreach (char c in scannedData)
                {
                    if (in_track_1)
                    {
                        #region Track1

                        if (!track1_caret1_found)
                        {
                            #region Get-Track1-CCN

                            if ((c != '%') && (c != 'B') && (c != '^'))
                            {
                                track1_ccn += c;
                            }

                            if (c == '^')
                            {
                                track1_caret1_found = true;
                                track1 += c;
                                continue;
                            }

                            #endregion
                        }

                        if (track1_caret1_found && !track1_caret2_found)
                        {
                            #region Get-Cardholder-Name

                            if (c != '^')
                            {
                                track1_cardholder += c;
                            }
                            else
                            {
                                track1_caret2_found = true;
                                track1 += c;
                                continue;
                            }

                            #endregion
                        }

                        if (track1_caret1_found && track1_caret2_found)
                        {
                            #region Get-Expiration-and-CVV

                            if (track1_leg3_count == 0) track1_expyr += c;
                            if (track1_leg3_count == 1) track1_expyr += c;
                            if (track1_leg3_count == 2) track1_expmo += c;
                            if (track1_leg3_count == 3) track1_expmo += c;
                            if (track1_leg3_count == 22) track1_cvv += c;
                            if (track1_leg3_count == 23) track1_cvv += c;
                            if (track1_leg3_count == 24) track1_cvv += c;
                            track1_leg3_count++;

                            #endregion
                        }

                        track1 += c;
                        if (c == '?')
                        {
                            in_track_1 = false;
                            in_track_2 = true;
                            continue;
                        }

                        #endregion
                    }

                    if (in_track_2)
                    {
                        #region Track2

                        if (!track2_equals_found)
                        {
                            #region Get-Track2-CCN

                            if ((c != ';') && (c != '='))
                            {
                                track2_ccn += c;
                            }

                            if (c == '=')
                            {
                                track2_equals_found = true;
                                track2 += c;
                                continue;
                            }

                            #endregion
                        }

                        if (track2_equals_found)
                        {
                            #region Get-Expiration-and-Encrypted-PIN

                            if (track2_leg2_count == 0) track2_expyr += c;
                            if (track2_leg2_count == 1) track2_expyr += c;
                            if (track2_leg2_count == 2) track2_expmo += c;
                            if (track2_leg2_count == 3) track2_expmo += c;
                            if (track2_leg2_count == 8) track2_encpin += c;
                            if (track2_leg2_count == 9) track2_encpin += c;
                            if (track2_leg2_count == 10) track2_encpin += c;
                            track2_leg2_count++;

                            #endregion
                        }

                        track2 += c;
                        if (c == '?')
                        {
                            in_track_2 = false;
                            in_track_3 = true;
                            continue;
                        }

                        #endregion
                    }

                    if (in_track_3)
                        track3 += c;
                }

                CardHolder = track1_cardholder;
                CardNumber = string.IsNullOrEmpty(track1_ccn) ? track2_ccn : track1_ccn;
                ExpireMonth = string.IsNullOrEmpty(track1_expmo) ? track2_expmo : track1_expmo;
                ExpireYear = string.IsNullOrEmpty(track1_expyr) ? track2_expyr : track1_expyr;
                //Valid = Validate();
                Valid = true;
            }
            catch
            {
                Valid = false;
            }
        }

        private bool Validate()
        {
            try
            {
                if (CardNumber.Length < 12 || CardNumber.Length > 19)
                    return false;
                var IIN = CardNumber.Substring(0, 6);
                CardType = eCardType.Unknown;
                if (IIN.StartsWith("34") || IIN.StartsWith("37"))
                    CardType = eCardType.AmericanExpress;
                else if (IIN.StartsWith("4"))
                {
                    if (IIN.StartsWith("4026") || IIN.StartsWith("417500") || IIN.StartsWith("4405") || IIN.StartsWith("4508")
                        || IIN.StartsWith("4844") || IIN.StartsWith("4913") || IIN.StartsWith("4917"))
                        CardType = eCardType.VisaElectron;
                    else
                        CardType = eCardType.Visa;
                }
                else if (IIN.StartsWith("51") || IIN.StartsWith("52") || IIN.StartsWith("53") || IIN.StartsWith("54") 
                    || IIN.StartsWith("55"))
                    CardType = eCardType.MasterCard;
                else if (IIN.StartsWith("6011") || IIN.StartsWith("644") || IIN.StartsWith("65"))
                    CardType = eCardType.Discover;
                else if (IIN.StartsWith("36") || IIN.StartsWith("38") || IIN.StartsWith("39"))
                    CardType = eCardType.DinersClubInternational;
                else if (IIN.StartsWith("3"))
                {
                    var jcbCheck = Convert.ToInt32(IIN.Substring(0, 4));
                    if (jcbCheck >= 3528 && jcbCheck <= 3589)
                        CardType = eCardType.JCB;
                    else
                    {
                        var dinersCheck = Convert.ToInt32(IIN.Substring(0, 3));
                        if (dinersCheck < 306)
                            CardType = eCardType.DinersClubCarteBlanche;
                        else if (dinersCheck == 309)
                            CardType = eCardType.DinersClubInternational;
                        else
                            return false;
                    }
                }
                else
                    return false;

                //// 1.	Starting with the check digit double the value of every other digit 
                //// 2.	If doubling of a number results in a two digits number, add up
                ///   the digits to get a single digit number. This will results in eight single digit numbers                    
                //// 3. Get the sum of the digits
                int sumOfDigits = CardNumber.Where((e) => e >= '0' && e <= '9')
                                    .Reverse()
                                    .Select((e, i) => ((int)e - 48) * (i % 2 == 0 ? 1 : 2))
                                    .Sum((e) => e / 10 + e % 10);


                //// If the final sum is divisible by 10, then the credit card number
                //   is valid. If it is not divisible by 10, the number is invalid.            
                return sumOfDigits % 10 == 0;
            }
            catch
            {
                return false;
            }
        }
    }

    public enum eCardType
    {
        Unknown = 0,
        AmericanExpress = 1,
        Visa = 2,
        VisaElectron = 3,
        MasterCard = 4,
        Discover = 5,
        JCB = 6,
        DinersClub = 7,
        DinersClubCarteBlanche = 8,
        DinersClubInternational = 9
    }


    public class InventoryRecord
    {
        [JsonProperty("ArtistNumber")]
        public int ArtistNumber { get; set; }
        [JsonProperty("DisplayName")]
        public string DisplayName { get; set; }
        [JsonProperty("LastName")]
        public string LastName { get; set; }
        [JsonProperty("Email")]
        public string Email { get; set; }
        [JsonProperty("LocationCode")]
        public string LocationCode { get; set; }
        [JsonProperty("ArtShowPieces")]
        public int ArtShowPieces { get; set; }
        [JsonProperty("PrintShopPieces")]
        public int PrintShopPieces { get; set; }
    }

    public class ArtistWithWaivedFees
    {
        [JsonProperty("DisplayName")]
        public string ArtistName { get; set; }
        [JsonProperty("FeesWaivedReason")]
        public string WaiverReason { get; set; }
    }

    public class ArtistSummary
    {
        [JsonProperty("DisplayName")]
        public string DisplayName { get; set; }
        [JsonProperty("LegalName")]
        public string LegalName { get; set; }
        [JsonProperty("Address1")]
        public string Address1 { get; set; }
        [JsonProperty("Address2")]
        public string Address2 { get; set; }
        [JsonProperty("City")]
        public string City { get; set; }
        [JsonProperty("State")]
        public string State { get; set; }
        [JsonProperty("ZipCode")]
        public string ZipCode { get; set; }
        [JsonProperty("Country")]
        public string Country { get; set; }
        [JsonProperty("StartDate")]
        public DateTime StartDate { get; set; }
        [JsonProperty("EndDate")]
        public DateTime EndDate { get; set; }
        [JsonProperty("Location")]
        public string Location { get; set; }
    }

    public class ReceiptDetails
    {
        [JsonProperty("RecordID")]
        public int RecordID { get; set; }
        [JsonProperty("PurchaserID")]
        public int? PurchaserID { get; set; }
        [JsonProperty("PurchaserOneTimeID")]
        public int? PurchaserOneTimeID { get; set; }
        [JsonProperty("FirstName")]
        public string FirstName { get; set; }
        [JsonProperty("LastName")]
        public string LastName { get; set; }
        [JsonProperty("BadgeName")]
        public string BadgeName { get; set; }
        [JsonProperty("ItemTypeName")]
        public string ItemTypeName { get; set; }
        [JsonProperty("TotalPrice")]
        public decimal TotalPrice { get; set; }
        [JsonProperty("PiecePrint")]
        public decimal? PiecePrint { get; set; }
        [JsonProperty("DisplayName")]
        public string DisplayName { get; set; }
        [JsonProperty("Title")]
        public string Title { get; set; }
        [JsonProperty("Purchased")]
        public DateTime Purchased { get; set; }
        [JsonProperty("PaymentSource")]
        public string PaymentSource { get; set; }
        [JsonProperty("PaymentReference")]
        public string PaymentReference { get; set; }
        [JsonProperty("CheckNumber")]
        public string CheckNumber { get; set; }
    }
}

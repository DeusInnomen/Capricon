using System;
using System.Collections;
using System.Collections.Generic;
using System.IO;
using System.Linq;
using System.Net;
using System.Text;
using System.Windows.Forms;
using CryptSharp;
using DYMO.Label.Framework;
using Newtonsoft.Json;
using System.Security;

namespace Registration
{
    public class BoolConverter : JsonConverter
    {
        public override void WriteJson(JsonWriter writer, object value, JsonSerializer serializer)
        {
            writer.WriteValue(((bool) value) ? 1 : 0);
        }

        public override object ReadJson(JsonReader reader, Type objectType, object existingValue, JsonSerializer serializer)
        {
            return reader.Value.ToString() == "1";
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
        [JsonProperty("Banned"), JsonConverter(typeof (BoolConverter))]
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
            {
                payload += "SaveNewPerson";
                if (!string.IsNullOrEmpty(Email))
                {
                    var hash = "NotYetActivated";
                    if (!string.IsNullOrEmpty(newPassword))
                    {
                        hash = Crypter.Blowfish.Crypt(newPassword, new CrypterOptions
                            {
                                {CrypterOption.Rounds, 13},
                                {CrypterOption.Variant, BlowfishCrypterVariant.Corrected}
                            });
                    }
                    payload += "&password=" + hash;
                }
            }
            payload += "&person=" + JsonConvert.SerializeObject(this);
            var data = Encoding.ASCII.GetBytes(payload);

            var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            request.Timeout = 20000;
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var webResponse = (HttpWebResponse)request.GetResponse();
            var results = new StreamReader(webResponse.GetResponseStream()).ReadToEnd();
            var response = JsonConvert.DeserializeObject<Dictionary<string, dynamic>>(results);
            if (response.ContainsKey("error"))
            {
                LastError = (string) response["error"];
                return false;
            }
            LastError = null;
            if(response.ContainsKey("peopleID"))
                PeopleID = Convert.ToInt32(response["peopleID"]);
            if (response.ContainsKey("oneTimeID"))
                OneTimeID = Convert.ToInt32(response["oneTimeID"]);
            return true;
        }
    }

    public class Badge
    {
        [JsonProperty("BadgeID")]
        public int BadgeID { get; set; }
        [JsonProperty("BadgeNumber")]
        public int BadgeNumber { get; set; }
        [JsonProperty("BadgeName")]
        public string BadgeName { get; set; }
        [JsonProperty("FirstName")]
        public string FirstName { get; set; }
        [JsonProperty("LastName")]
        public string LastName { get; set; }
        [JsonProperty("BadgeTypeID")]
        public int BadgeTypeID { get; set; }
        [JsonProperty("BadgeDescription")]
        public string Description { get; set; }
        [JsonProperty("ParentName")]
        public string ParentName { get; set; }
        [JsonProperty("ParentContact")]
        public string ParentContact { get; set; }
        [JsonProperty("Status")]
        public string Status { get; set; }

        public string Name { get { return (FirstName + " " + LastName).Trim(); } }

        public override string ToString()
        {
            return BadgeNumber + ": " + BadgeName;
        }

        public void Print(bool backOnly = false)
        {
            Badge.PrintBadge(this, backOnly);
        }

        public static Badge GetBadge(int badgeID)
        {
            var data = Encoding.ASCII.GetBytes("action=GetBadges&id=" + badgeID);
            var request = WebRequest.Create(Program.URL + "/functions/userQuery.php");
            request.ContentLength = data.Length;
            request.ContentType = "application/x-www-form-urlencoded";
            request.Method = "POST";
            request.Timeout = 20000;
            using (var stream = request.GetRequestStream())
                stream.Write(data, 0, data.Length);

            var response = (HttpWebResponse)request.GetResponse();
            var responseJson = new StreamReader(response.GetResponseStream()).ReadToEnd();
            return JsonConvert.DeserializeObject<Badge>(responseJson);            
        }

        public static void PrintBadge(Badge badge, bool printFront = true, bool printBack = true)
        {
            var labelData = Encoding.ASCII.GetString(Properties.Resources.BadgeFront);
            var label = DYMO.Label.Framework.Label.OpenXml(labelData);
            var builder = new LabelSetBuilder();
            var record = builder.AddRecord();
            if (printFront)
            {
                record.AddTextMarkup("BadgeName", "<font family=\"Arial\" size=\"32\">" + SecurityElement.Escape(badge.BadgeName) + "</font>");
                record.AddTextMarkup("BadgeNumber", "<font family=\"Lucida Console\" size=\"22\">" + badge.BadgeNumber + "</font>");
                label.Print(Framework.GetLabelWriterPrinters().First(), null, builder.Xml);
            }

            if (printBack)
            {
                if (badge.BadgeTypeID == 2)
                {
                    labelData = Encoding.ASCII.GetString(Properties.Resources.BadgeBackKid);
                    label = DYMO.Label.Framework.Label.OpenXml(labelData);
                    builder = new LabelSetBuilder();
                    record = builder.AddRecord();
                    record.AddTextMarkup("RealName", "<font family=\"Arial\" size=\"18\">" + SecurityElement.Escape(badge.Name) + "</font>");
                    record.AddTextMarkup("ParentName", "<font family=\"Arial\" size=\"18\">" + SecurityElement.Escape(badge.ParentName) +
                        " " + SecurityElement.Escape(badge.ParentContact) + "</font>");
                    record.AddTextMarkup("BadgeType", "<font family=\"Arial\" size=\"18\">" + SecurityElement.Escape(badge.Description) + "</font>");
                    record.AddTextMarkup("BadgeNumber", "<font family=\"Lucida Console\" size=\"22\">" + badge.BadgeNumber + "</font>");
                    label.Print(Framework.GetLabelWriterPrinters().First(), null, builder.Xml);
                }
                else
                {
                    labelData = Encoding.ASCII.GetString(Properties.Resources.BadgeBack);
                    label = DYMO.Label.Framework.Label.OpenXml(labelData);
                    builder = new LabelSetBuilder();
                    record = builder.AddRecord();
                    record.AddTextMarkup("RealName", "<font family=\"Arial\" size=\"24\">" + SecurityElement.Escape(badge.Name) + "</font>");
                    record.AddTextMarkup("BadgeType", "<font family=\"Arial\" size=\"24\">" + SecurityElement.Escape(badge.Description) + "</font>");
                    record.AddTextMarkup("BadgeNumber", "<font family=\"Lucida Console\" size=\"22\">" + badge.BadgeNumber + "</font>");
                    label.Print(Framework.GetLabelWriterPrinters().First(), null, builder.Xml);
                }
            }
        }
    }

    public class GeneratedBadge
    {
        [JsonProperty("BadgeID")]
        public int BadgeID { get; set; }
        [JsonProperty("PeopleID")]
        public int? PeopleID { get; set; }
        [JsonProperty("OneTimeID")]
        public int? OneTimeID { get; set; }
        [JsonProperty("BadgeName")]
        public string BadgeName { get; set; }
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
        [JsonProperty("BadgeNumber")]
        public int BadgeNumber { get; set; }
        [JsonProperty("Email")]
        public string Email { get; set; }

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
    }

    public class SaleItem
    {
        [JsonProperty("AvailableBadgeID")]
        public int ID { get; set; }
        [JsonProperty("BadgeTypeID")]
        public int TypeID { get; set; }
        [JsonProperty("Description")]
        public string Description { get; set; }
        [JsonProperty("CategoryID")]
        public int CategoryID { get; set; }
        [JsonProperty("CategoryName")]
        public string Category { get; set; }
        [JsonProperty("Year")]
        public int Year { get; set; }
        [JsonProperty("Price")]
        public decimal Price { get; set; }
        [JsonProperty("AvailableTo")]
        public DateTime AvailableUntil { get; set; }
        [JsonProperty("Details")]
        public string Details { get; set; }
        [JsonProperty("Discount")]
        public decimal Discount { get; set; }
        [JsonProperty("RecipientPeopleID")]
        public int? RecipientPeopleID { get; set; }
        [JsonProperty("RecipientOneTimeID")]
        public int? RecipientOneTimeID { get; set; }

        internal SaleItem Clone()
        {
            var copy = new SaleItem
                {
                    ID = ID,
                    TypeID = TypeID,
                    Description = Description,
                    CategoryID = CategoryID,
                    Category = Category,
                    Year = Year,
                    Price = Price,
                    AvailableUntil = AvailableUntil,
                    Details = Details,
                    Discount = Discount,
                    RecipientPeopleID = RecipientPeopleID,
                    RecipientOneTimeID = RecipientOneTimeID
                };
            return copy;
        }
    }

    public class Discount
    {
        public decimal? Amount { get; set; }
        public int? FreeBadges { get; set; }
        public decimal? Value { get; set; } 
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
}

<?php
session_start();

// Check if the 'admin' session variable is not set or is false (user not logged in)
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true || !isset($_SESSION['adminID'])) {
    // Redirect the user to the login page
    header("Location: Admin Login.php");
    exit(); // Terminate the script
}

// Include your database connection file
require_once 'backend/pawfect_connect.php';

// Get the AdminID from the sessionw
$adminID = $_SESSION['adminID'];

// Prepare and execute the SQL query to fetch admin information
$sql = "SELECT * FROM admininformation WHERE AdminID = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $adminID);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Check if there is a row returned
if ($row = mysqli_fetch_assoc($result)) {
    // Admin information retrieved successfully
    $firstName = $row['firstname'];
 
    $lastName = $row['lastname'];
    $adminPhoto = $row['adminphoto'];

    // Now you can use these variables to display the admin information in your HTML
} else {
    // Admin information not found
    echo "Admin information not found!";
}
if (isset($_GET['patientID'])) {
  $patientID = $_GET['patientID'];

  // SQL query to fetch patient details
  $sql = "
  SELECT 
      p.LastName, 
      p.FirstName, 
      p.MiddleName, 
      TIMESTAMPDIFF(YEAR, p.BirthDate, CURDATE()) AS Age, 
      p.BirthDate, 
      p.Weight, 
      p.Sex, 
      ci.LineNumber AS ContactNumber, 
      ci.EmailAddress, 
      pa.Province, 
      pa.City, 
      pa.Address, 
      ec.FullName AS EmergencyContactName, 
      ec.Relationship AS EmergencyContactRelationship, 
      ec.LineNumber AS EmergencyContactNumber
  FROM 
      patient p
  LEFT JOIN 
      contactinformation ci ON p.PatientID = ci.PatientID
  LEFT JOIN 
      patientaddress pa ON p.PatientID = pa.PatientID
  LEFT JOIN 
      emergencycontact ec ON p.PatientID = ec.PatientID
  WHERE 
      p.PatientID = ?
  ";

  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $patientID);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
      // Store patient details in variables
      $lastName = $row['LastName'];
      $firstName = $row['FirstName'];
      $middleName = $row['MiddleName'];
      $age = $row['Age'];
      $birthDate = $row['BirthDate'];
      $weight = $row['Weight'];
      $sex = $row['Sex'];
      $contactNumber = $row['ContactNumber'];
      $emailAddress = $row['EmailAddress'];
      $currentProvince = $row['Province'];
      $currentCity = $row['City'];
      $address = $row['Address'];
      $emergencyContactName = $row['EmergencyContactName'];
      $emergencyContactRelationship = $row['EmergencyContactRelationship'];
      $emergencyContactNumber = $row['EmergencyContactNumber'];
  } else {
      echo "No patient found with the given ID.";
      exit(); // Terminate the script
  }
} else {
  echo "No PatientID provided.";
  exit(); // Terminate the script
}
$provincesAndCities = array(
    "Abra" => array("Bangued", "Boliney", "Bucay", "Bucloc", "Daguioman", "Danglas", "Dolores", "La Paz", "Lacub", "Lagangilang", "Lagayan", "Langiden", "Licuan-Baay", "Luba", "Malibcong", "Manabo", "Penarrubia", "Pidigan", "Pilar", "Sallapadan", "San Isidro", "San Juan", "San Quintin", "Tayum", "Tineg", "Tubo", "Villaviciosa"),
    "Agusan del Norte" => array("Buenavista", "Butuan City", "Cabadbaran City", "Carmen", "Jabonga", "Kitcharao", "Las Nieves", "Magallanes", "Nasipit", "Remedios T. Romualdez", "Santiago", "Tubay"),
    "Agusan del Sur" => array("Bayugan City", "Bunawan", "Esperanza", "La Paz", "Loreto", "Prosperidad (Capital)", "Rosario", "San Francisco", "San Luis", "Santa Josefa", "Sibagat", "Talacogon", "Trento", "Veruela"),
    "Aklan" => array("Altavas", "Balete", "Banga", "Batan", "Buruanga", "Ibajay", "Kalibo (Capital)", "Lezo", "Libacao", "Madalag", "Makato", "Malay", "Malinao", "Nabas", "New Washington", "Numancia", "Tangalan"),
    "Albay" => array("Bacacay", "Camalig", "Daraga (Locsin)", "Guinobatan", "Jovellar", "Legazpi City (Capital)", "Libon", "Ligao City", "Malilipot", "Malinao", "Manito", "Oas", "Pio Duran", "Polangui", "Rapu-Rapu", "Santo Domingo", "Tiwi"),
    "Antique" => array("Anini-y", "Barbaza", "Belison", "Bugasong", "Caluya", "Culasi", "Hamtic", "Laua-an", "Libertad", "Pandan", "Patnongon", "San Jose de Buenavista (Capital)", "San Remigio", "Sebaste", "Sibalom", "Tibiao", "Tobias Fornier", "Valderrama"),
    "Apayao" => array("Calanasan (Bayag)", "Conner", "Flora", "Kabugao (Capital)", "Luna", "Pudtol", "Santa Marcela"),
    "Aurora" => array("Baler (Capital)", "Casiguran", "Dilasag", "Dinalungan", "Dingalan", "Dipaculao", "Maria Aurora", "San Luis"),
    "Basilan" => array("Akbar", "Al-Barka", "Hadji Mohammad Ajul", "Hadji Muhtamad", "Isabela City (Capital)", "Lamitan City", "Lantawan", "Maluso", "Sumisip", "Tabuan-Lasa", "Tipo-Tipo", "Ungkaya Pukan"),
    "Bataan" => array("Abucay", "Bagac", "Balanga City (Capital)", "Dinalupihan", "Hermosa", "Limay", "Mariveles", "Morong", "Orani", "Orion", "Pilar", "Samal"),
    "Batanes" => array("Basco (Capital)", "Itbayat", "Ivana", "Mahatao", "Sabtang", "Uyugan"),
    "Batangas" => array("Agoncillo", "Alitagtag", "Balayan", "Balete", "Bauan", "Calaca", "Calatagan", "Cuenca", "Ibaan", "Laurel", "Lemery", "Lian", "Lipa City", "Lobo", "Mabini", "Malvar", "Mataasnakahoy", "Nasugbu", "Padre Garcia", "Rosario", "San Jose", "San Juan", "San Luis", "San Nicolas", "San Pascual", "Santa Teresita", "Santo Tomas", "Taal", "Talisay", "Tanauan City", "Taysan", "Tingloy", "Tuy"),
    "Benguet" => array("Atok", "Baguio City", "Bakun", "Bokod", "Buguias", "Itogon", "Kabayan", "Kapangan", "Kibungan", "La Trinidad (Capital)", "Mankayan", "Sablan", "Tuba", "Tublay"),
    "Biliran" => array("Almeria", "Biliran", "Cabucgayan", "Caibiran", "Culaba", "Kawayan", "Maripipi", "Naval (Capital)"),
    "Bohol" => array("Alburquerque", "Alicia", "Anda", "Antequera", "Baclayon", "Balilihan", "Batuan", "Bien Unido", "Bilar", "Buenavista", "Calape", "Candijay", "Carmen", "Catigbian", "Clarin", "Corella", "Cortes", "Dagohoy", "Danao", "Dauis", "Dimiao", "Duero", "Garcia Hernandez", "Getafe", "Guindulman", "Inabanga", "Jagna", "Lila", "Loay", "Loboc", "Loon", "Mabini", "Maribojoc", "Panglao", "Pilar", "President Carlos P. Garcia (Pitogo)", "Sagbayan (Borja)", "San Isidro", "San Miguel", "Sevilla", "Sierra Bullones", "Sikatuna", "Tagbilaran City (Capital)", "Talibon", "Trinidad", "Tubigon", "Ubay", "Valencia"),
    "Bukidnon" => array("Baungon", "Cabanglasan", "Damulog", "Dangcagan", "Don Carlos", "Impasugong", "Kadingilan", "Kalilangan", "Kibawe", "Kitaotao", "Lantapan", "Libona", "Malaybalay City (Capital)", "Malitbog", "Manolo Fortich", "Maramag", "Pangantucan", "Quezon", "San Fernando", "Sumilao", "Talakag"),
    "Bulacan" => array("Angat", "Balagtas (Bigaa)", "Baliuag", "Bocaue", "Bulacan", "Bustos", "Calumpit", "Doña Remedios Trinidad", "Guiguinto", "Hagonoy", "Malolos City (Capital)", "Marilao", "Meycauayan City", "Norzagaray", "Obando", "Pandi", "Paombong", "Plaridel", "Pulilan", "San Ildefonso", "San Jose del Monte City", "San Miguel", "San Rafael", "Santa Maria"),
    "Cagayan" => array("Abulug", "Alcala", "Allacapan", "Amulung", "Aparri", "Baggao", "Ballesteros", "Buguey", "Calayan", "Camalaniugan", "Claveria", "Enrile", "Gattaran", "Gonzaga", "Iguig", "Lal-lo", "Lasam", "Pamplona", "Peñablanca", "Piat", "Rizal", "Sanchez-Mira", "Santa Ana", "Santa Praxedes", "Santa Teresita", "Santo Niño (Faire)", "Solana", "Tuao", "Tuguegarao City (Capital)"),
    "Camarines Norte" => array("Basud", "Capalonga", "Daet (Capital)", "Jose Panganiban", "Labo", "Mercedes", "Paracale", "San Lorenzo Ruiz (Imelda)", "San Vicente", "Santa Elena", "Talisay", "Vinzons"),
    "Camarines Sur" => array("Baao", "Balatan", "Bato", "Bombon", "Buhi", "Bula", "Cabusao", "Calabanga", "Camaligan", "Canaman", "Caramoan", "Del Gallego", "Gainza", "Garchitorena", "Goa", "Iriga City", "Lagonoy", "Libmanan", "Lupi", "Magarao", "Milaor", "Minalabac", "Nabua", "Naga City (Capital)", "Ocampo", "Pamplona", "Pasacao", "Pili (Capital)", "Presentacion (Parubcan)", "Ragay", "Sagnay", "San Fernando", "San Jose", "Sipocot", "Siruma", "Tigaon", "Tinambac"),
    "Camiguin" => array("Catarman", "Guinsiliban", "Mahinog", "Mambajao (Capital)"),
    "Capiz" => array("Cuartero", "Dao", "Dumalag", "Dumarao", "Ivisan", "Jamindan", "Maayon", "Mambusao", "Panay", "Panitan", "Pilar", "Pontevedra", "President Roxas", "Roxas City (Capital)", "Sapi-an", "Sigma", "Tapaz"),
    "Catanduanes" => array("Bagamanoc", "Baras", "Bato", "Caramoran", "Gigmoto", "Pandan", "Panganiban (Payo)", "San Andres (Calolbon)", "San Miguel", "Viga", "Virac (Capital)"),
    "Cavite" => array("Alfonso", "Amadeo", "Bacoor City", "Carmona", "Cavite City", "Dasmariñas City", "General Emilio Aguinaldo", "General Mariano Alvarez", "General Trias", "Imus City (Capital)", "Indang", "Kawit", "Magallanes", "Maragondon", "Mendez", "Naic", "Noveleta", "Rosario", "Silang", "Tagaytay City", "Tanza", "Ternate", "Trece Martires City (Capital)"),
    "Cebu" => array("Alcantara", "Alcoy", "Alegria", "Aloguinsan", "Argao", "Asturias", "Badian", "Balamban", "Bantayan", "Barili", "Bogo City", "Boljoon", "Borbon", "Carcar City", "Carmen", "Catmon", "Cebu City (Capital)", "Compostela", "Consolacion", "Cordoba", "Daanbantayan", "Dalaguete", "Danao City", "Dumanjug", "Ginatilan", "Liloan", "Madridejos", "Malabuyoc", "Mandaue City", "Medellin", "Minglanilla", "Moalboal", "Naga City", "Oslob", "Pilar", "Pinamungajan", "Poro", "Ronda", "Samboan", "San Fernando", "San Francisco", "San Remigio", "Santa Fe", "Santander", "Sibonga", "Sogod", "Tabogon", "Tabuelan", "Talisay City", "Toledo City", "Tuburan", "Tudela"),
    "Cotabato" => array("Alamada", "Aleosan", "Antipas", "Arakan", "Banisilan", "Carmen", "Kabacan", "Kidapawan City (Capital)", "Libungan", "M'lang", "Magpet", "Makilala", "Matalam", "Midsayap", "Pigcawayan", "Pikit", "President Roxas", "Roxas", "Tulunan"),
    "Davao de Oro" => array("Compostela", "Laak (San Vicente)", "Mabini (Dona Alicia)", "Maco", "Maragusan (San Mariano)", "Mawab", "Monkayo", "Montevista", "Nabunturan (Capital)", "New Bataan", "Pantukan"),
    "Davao del Norte" => array("Asuncion (Saug)", "Braulio E. Dujali", "Carmen", "Kapalong", "New Corella", "Panabo City", "Samal City", "San Isidro", "Santo Tomas", "Tagum City (Capital)", "Talaingod"),
    "Davao del Sur" => array("Bansalan", "Davao City", "Digos City (Capital)", "Hagonoy", "Kiblawan", "Magsaysay", "Malalag", "Matanao", "Padada", "Santa Cruz", "Sulop"),
    "Davao Occidental" => array("Don Marcelino", "Jose Abad Santos (Trinidad)", "Malita (Capital)", "Santa Maria", "Sarangani"),
    "Davao Oriental" => array("Baganga", "Banaybanay", "Boston", "Caraga", "Cateel", "Governor Generoso", "Lupon", "Manay", "Mati City (Capital)", "San Isidro", "Tarragona"),
    "Dinagat Islands" => array("Basilisa (Rizal)", "Cagdianao", "Dinagat", "Loreto", "San Jose (Capital)"),
    "Eastern Samar" => array("Arteche", "Balangiga", "Balangkayan", "Borongan City (Capital)", "Can-avid", "Dolores", "General MacArthur", "Giporlos", "Guiuan", "Hernani", "Jipapad", "Lawaan", "Llorente", "Maslog", "Maydolong", "Mercedes", "Oras", "Quinapondan", "Salcedo", "San Julian", "San Policarpo", "Sulat", "Taft"),
    "Guimaras" => array("Buenavista", "Jordan (Capital)", "Nueva Valencia", "San Lorenzo", "Sibunag"),
    "Ifugao" => array("Aguinaldo", "Alfonso Lista (Potia)", "Asipulo", "Banaue", "Hingyon", "Hungduan", "Kiangan", "Lagawe (Capital)", "Lamut", "Mayoyao", "Tinoc"),
    "Ilocos Norte" => array("Adams", "Bacarra", "Badoc", "Bangui", "Banna (Espiritu)", "Batac City", "Burgos", "Carasi", "Currimao", "Dingras", "Dumalneg", "Laoag City (Capital)", "Marcos", "Nueva Era", "Pagudpud", "Paoay", "Pasuquin", "Piddig", "Pinili", "San Nicolas", "Sarrat", "Solsona", "Vintar"),
    "Ilocos Sur" => array("Alilem", "Banayoyo", "Bantay", "Burgos", "Cabugao", "Candon City", "Caoayan", "Cervantes", "Galimuyod", "Gregorio del Pilar (Concepcion)", "Lidlidda", "Magsingal", "Nagbukel", "Narvacan", "Quirino (Angkaki)", "Salcedo (Baugen)", "San Emilio", "San Esteban", "San Ildefonso", "San Juan (Lapog)", "San Vicente", "Santa", "Santa Catalina", "Santa Cruz", "Santa Lucia", "Santa Maria", "Santiago", "Santo Domingo", "Sigay", "Sinait", "Sugpon", "Suyo", "Tagudin", "Vigan City (Capital)"),
    "Iloilo" => array("Ajuy", "Alimodian", "Anilao", "Badiangan", "Balasan", "Banate", "Barotac Nuevo", "Barotac Viejo", "Batad", "Bingawan", "Cabatuan", "Calinog", "Carles", "Concepcion", "Dingle", "Dueñas", "Dumangas", "Estancia", "Guimbal", "Igbaras", "Iloilo City (Capital)", "Janiuay", "Lambunao", "Leganes", "Lemery", "Leon", "Maasin", "Miagao", "Mina", "New Lucena", "Oton", "Passi City", "Pavia", "Pototan", "San Dionisio", "San Enrique", "San Joaquin", "San Miguel", "San Rafael", "Santa Barbara", "Sara", "Tigbauan", "Tubungan", "Zarraga"),
    "Isabela" => array("Alicia", "Angadanan", "Aurora", "Benito Soliven", "Burgos", "Cabagan", "Cabatuan", "Cordon", "Delfin Albano (Magsaysay)", "Dinapigue", "Divilacan", "Echague", "Gamu", "Ilagan City (Capital)", "Jones", "Luna", "Maconacon", "Mallig", "Naguilian", "Palanan", "Quezon", "Quirino", "Ramon", "Reina Mercedes", "Roxas", "San Agustin", "San Guillermo", "San Isidro", "San Manuel", "San Mariano", "San Mateo", "San Pablo", "Santa Maria", "Santiago City", "Santo Tomas", "Tumauini"),
    "Kalinga" => array("Balbalan", "Lubuagan", "Pasil", "Pinukpuk", "Rizal (Liwan)", "Tabuk City (Capital)", "Tanudan", "Tinglayan"),
    "La Union" => array("Agoo", "Aringay", "Bacnotan", "Bagulin", "Balaoan", "Bangar", "Bauang", "Burgos", "Caba", "Luna", "Naguilian", "Pugo", "Rosario", "San Fernando City (Capital)", "San Gabriel", "San Juan", "Santo Tomas", "Santol", "Sudipen", "Tubao"),
    "Laguna" => array("Alaminos", "Bay", "Biñan City", "Cabuyao City", "Calamba City (Capital)", "Calauan", "Cavinti", "Famy", "Kalayaan", "Liliw", "Los Baños", "Luisiana", "Lumban", "Mabitac", "Magdalena", "Majayjay", "Nagcarlan", "Paete", "Pagsanjan", "Pakil", "Pangil", "Pila", "Rizal", "San Pablo City", "San Pedro City", "Santa Cruz (Capital)", "Santa Maria", "Santa Rosa City", "Siniloan", "Victoria"),
    "Lanao del Norte" => array("Bacolod", "Baloi", "Baroy", "Iligan City", "Kapatagan", "Kauswagan", "Kolambugan", "Lala", "Linamon", "Magsaysay", "Maigo", "Matungao", "Munai", "Nunungan", "Pantao Ragat", "Pantar", "Poona Bayabao (Gata)", "Salvador", "Sapad", "Sultan Naga Dimaporo (Karomatan)", "Tagoloan", "Tangcal", "Tubod (Capital)"),
    "Lanao del Sur" => array("Bacolod-Kalawi (Bacolod Grande)", "Balabagan", "Balindong (Watu)", "Bayang", "Binidayan", "Buadiposo-Buntong", "Bubong", "Butig", "Calanogas", "Ditsaan-Ramain", "Ganassi", "Kapai", "Kapatagan", "Lumba-Bayabao (Maguing)", "Lumbaca-Unayan", "Lumbatan", "Lumbayanague", "Madalum", "Madamba (Pagayawan)", "Maguing", "Malabang", "Marantao", "Marogong", "Masiu", "Mulondo", "Pagayawan (Tatarikan)", "Piagapo", "Picong (Sultan Gumander)", "Poona Bayabao (Gata)", "Pualas", "Saguiaran", "Sultan Dumalondong", "Picong", "Tagoloan II", "Tamparan", "Taraka", "Tubaran", "Tugaya", "Wao"),
    "Leyte" => array("Abuyog", "Alangalang", "Albuera", "Babatngon", "Barugo", "Bato", "Baybay City", "Burauen", "Calubian", "Capoocan", "Carigara", "Dagami", "Dulag", "Hilongos", "Hindang", "Inopacan", "Isabel", "Jaro", "Javier (Bugho)", "Julita", "Kananga", "La Paz", "Leyte", "MacArthur", "Mahaplag", "Matag-ob", "Matalom", "Mayorga", "Merida", "Ormoc City", "Palo", "Palompon", "Pastrana", "San Isidro", "San Miguel", "Santa Fe", "Tabango", "Tabontabon", "Tacloban City (Capital)", "Tanauan", "Tolosa", "Tunga", "Villaba"),
    "Maguindanao" => array("Ampatuan", "Barira", "Buldon", "Buluan", "Datu Abdullah Sangki", "Datu Anggal Midtimbang", "Datu Blah T. Sinsuat", "Datu Hoffer Ampatuan", "Datu Montawal (Pagagawan)", "Datu Odin Sinsuat (Dinaig)", "Datu Paglas", "Datu Piang (Dulawan)", "Datu Salibo", "Datu Saudi-Ampatuan", "Datu Unsay", "General Salipada K. Pendatun", "Guindulungan", "Kabuntalan (Tumbao)", "Mamasapano", "Mangudadatu", "Matanog", "Northern Kabuntalan", "Pagalungan", "Paglat", "Pandag", "Parang", "Rajah Buayan", "Shariff Aguak (Maganoy)", "South Upi", "Sultan Kudarat", "Sultan Mastura", "Sultan Sa Barongis (Lambayong)", "Sultan Sumagka (Talitay)", "Talayan", "Talitay", "Upi"),
    "Marinduque" => array("Boac (Capital)", "Buenavista", "Gasan", "Mogpog", "Santa Cruz", "Torrijos"),
    "Masbate" => array("Aroroy", "Baleno", "Balud", "Batuan", "Cataingan", "Cawayan", "Claveria", "Dimasalang", "Esperanza", "Mandaon", "Masbate City (Capital)", "Milagros", "Mobo", "Monreal", "Palanas", "Pio V. Corpuz (Limbuhan)", "Placer", "San Fernando", "San Jacinto", "San Pascual", "Uson"),
    "Misamis Occidental" => array("Aloran", "Baliangao", "Bonifacio", "Calamba", "Clarin", "Concepcion", "Don Victoriano Chiongbian (Don Mariano Marcos)", "Jimenez", "Lopez Jaena", "Oroquieta City (Capital)", "Ozamiz City", "Panaon", "Plaridel", "Sapang Dalaga", "Sinacaban", "Tangub City", "Tudela"),
    "Misamis Oriental" => array("Alubijid", "Balingasag", "Balingoan", "Binuangan", "Cagayan de Oro City (Capital)", "Claveria", "El Salvador City", "Gingoog City", "Gitagum", "Initao", "Jasaan", "Kinoguitan", "Lagonglong", "Laguindingan", "Libertad", "Lugait", "Magsaysay (Linugos)", "Manticao", "Medina", "Naawan", "Opol", "Salay", "Sugbongcogon", "Tagoloan", "Talisayan", "Villanueva"),
    "Mountain Province" => array("Barlig", "Bauko", "Besao", "Bontoc (Capital)", "Natonin", "Paracelis", "Sabangan", "Sadanga", "Sagada", "Tadian"),
    "Negros Occidental" => array("Bacolod City", "Bago City", "Binalbagan", "Cadiz City", "Calatrava", "Candoni", "Cauayan", "Enrique B. Magalona (Saravia)", "Escalante City", "Himamaylan City", "Hinigaran", "Hinoba-an (Asia)", "Ilog", "Isabela", "Kabankalan City", "La Carlota City", "La Castellana", "Manapla", "Moises Padilla (Magallon)", "Murcia", "Pontevedra", "Pulupandan", "Sagay City", "Salvador Benedicto", "San Carlos City", "San Enrique", "Silay City", "Sipalay City", "Talisay City", "Toboso", "Valladolid", "Victorias City"),
    "Negros Oriental" => array("Amlan (Ayuquitan)", "Ayungon", "Bacong", "Bais City", "Basay", "Bayawan City (Tulong)", "Bindoy (Payabon)", "Canlaon City", "Dauin", "Dumaguete City (Capital)", "Guihulngan", "Jimalalud", "La Libertad", "Mabinay", "Manjuyod", "Pamplona", "San Jose", "Santa Catalina", "Siaton", "Sibulan", "Tanjay City", "Tayasan", "Valencia (Luzurriaga)", "Vallehermoso", "Zamboanguita"),
    "Northern Samar" => array("Allen", "Biri", "Bobon", "Capul", "Catubig", "Catarman (Capital)", "Gamay", "Laoang", "Lapinig", "Las Navas", "Lavezares", "Lope de Vega", "Mapanas", "Mondragon", "Palapag", "Pambujan", "Rosario", "San Antonio", "San Isidro", "San Jose", "San Roque", "San Vicente", "Silvino Lobos", "Victoria"),
    "Nueva Ecija" => array("Aliaga", "Bongabon", "Cabanatuan City", "Cabiao", "Carranglan", "Cuyapo", "Gabaldon (Bitulok & Sabani)", "Gapan City", "General Mamerto Natividad", "General Tinio (Papaya)", "Guimba", "Jaen", "Laur", "Licab", "Llanera", "Lupao", "Muñoz City", "Nampicuan", "Palayan City (Capital)", "Pantabangan", "Peñaranda", "Quezon", "Rizal", "San Antonio", "San Isidro", "San Jose City", "San Leonardo", "Santa Rosa", "Santo Domingo", "Science City of Muñoz", "Talavera", "Talugtug", "Zaragoza"),
    "Nueva Vizcaya" => array("Alfonso Castañeda", "Ambaguio", "Aritao", "Bagabag", "Bambang (Capital)", "Bayombong", "Diadi", "Dupax del Norte", "Dupax del Sur", "Kasibu", "Kayapa", "Quezon", "Santa Fe", "Solano", "Villaverde (Ibung)"),
    "Occidental Mindoro" => array("Abra de Ilog", "Calintaan", "Looc", "Lubang", "Magsaysay", "Mamburao (Capital)", "Paluan", "Rizal", "Sablayan", "San Jose", "Santa Cruz"),
    "Oriental Mindoro" => array("Baco", "Bansud", "Bongabong", "Bulalacao (San Pedro)", "Calapan City (Capital)", "Gloria", "Mansalay", "Naujan", "Pinamalayan", "Pola", "Puerto Galera", "Roxas", "San Teodoro", "Socorro", "Victoria"),
    "Palawan" => array("Aborlan", "Agutaya", "Araceli", "Balabac", "Bataraza", "Brooke's Point", "Busuanga", "Cagayancillo", "Coron", "Culion", "Cuyo", "Dumaran", "El Nido (Bacuit)", "Kalayaan", "Linapacan", "Magsaysay", "Narra", "Puerto Princesa City (Capital)", "Quezon", "Rizal (Marcos)", "Roxas", "San Vicente", "Sofronio Española", "Taytay"),
    "Pampanga" => array("Angeles City", "Apalit", "Arayat", "Bacolor", "Candaba", "Floridablanca", "Guagua", "Lubao", "Mabalacat City", "Macabebe", "Magalang", "Masantol", "Mexico", "Minalin", "Porac", "San Fernando City (Capital)", "San Luis", "San Simon", "Santa Ana", "Santa Rita", "Santo Tomas", "Sasmuan"),
    "Pangasinan" => array("Agno", "Aguilar", "Alaminos City", "Alcala", "Anda", "Asingan", "Balungao", "Bani", "Basista", "Bautista", "Bayambang", "Binalonan", "Binmaley", "Bolinao", "Bugallon", "Burgos", "Calasiao", "Dasol", "Infanta", "Labrador", "Laoac", "Lingayen (Capital)", "Mabini", "Malasiqui", "Manaoag", "Mangaldan", "Mangatarem", "Mapandan", "Natividad", "Pozorrubio", "Rosales", "San Carlos City", "San Fabian", "San Jacinto", "San Manuel", "San Nicolas", "San Quintin", "Santa Barbara", "Santa Maria", "Santo Tomas", "Sison", "Sual", "Tayug", "Umingan", "Urbiztondo", "Villasis"),
    "Quezon" => array("Agdangan", "Alabat", "Atimonan", "Buenavista", "Burdeos", "Calauag", "Candelaria", "Catanauan", "Dolores", "General Luna", "General Nakar", "Guinayangan", "Gumaca", "Infanta", "Jomalig", "Lopez", "Lucban", "Lucena City (Capital)", "Macalelon", "Mauban", "Mulanay", "Padre Burgos", "Pagbilao", "Panukulan", "Patnanungan", "Perez", "Pitogo", "Plaridel", "Polillo", "Quezon", "Real", "Sampaloc", "San Andres", "San Antonio", "San Francisco (Aurora)", "San Narciso", "Sariaya", "Tagkawayan", "Tayabas City", "Tiaong", "Unisan"),
    "Quirino" => array("Aglipay", "Cabarroguis (Capital)", "Diffun", "Maddela", "Nagtipunan", "Saguday"),
    "Rizal" => array("Angono", "Antipolo City", "Baras", "Binangonan", "Cainta", "Cardona", "Jalajala", "Morong", "Pililla", "Rodriguez (Montalban)", "San Mateo", "Tanay", "Taytay", "Teresa"),
    "Romblon" => array("Alcantara", "Banton (Jones)", "Cajidiocan", "Calatrava", "Concepcion", "Corcuera", "Ferrol", "Looc", "Magdiwang", "Odiongan (Capital)", "Romblon", "San Agustin", "San Andres", "San Fernando", "San Jose", "Santa Fe", "Santa Maria"),
    "Samar (Western Samar)" => array("Almagro", "Basey", "Calbayog City", "Calbiga", "Catbalogan City (Capital)", "Daram", "Gandara", "Hinabangan", "Jiabong", "Marabut", "Matuguinao", "Motiong", "Pagsanghan", "Paranas (Wright)", "Pinabacdao", "San Jorge", "San Jose de Buan", "San Sebastian", "Santa Margarita", "Santa Rita", "Santo Niño", "Tagapul-an", "Talalora", "Tarangnan", "Villareal", "Zumarraga"),
    "Sarangani" => array("Alabel (Capital)", "Glan", "Kiamba", "Maasim", "Maitum", "Malapatan", "Malungon"),
    "Siquijor" => array("Enrique Villanueva", "Larena", "Lazi", "Maria", "San Juan", "Siquijor (Capital)"),
    "Sorsogon" => array("Barcelona", "Bulan", "Bulusan", "Casiguran", "Castilla", "Donsol", "Gubat", "Irosin", "Juban", "Magallanes", "Matnog", "Pilar", "Prieto Diaz", "Santa Magdalena", "Sorsogon City (Capital)"),
    "South Cotabato" => array("Banga", "General Santos City (Dadiangas) (Capital)", "Koronadal City", "Lake Sebu", "Norala", "Polomolok", "Santo Niño", "Surallah", "T'boli", "Tampakan", "Tantangan", "Tupi"),
    "Southern Leyte" => array("Anahawan", "Bontoc", "Hinunangan", "Hinundayan", "Libagon", "Liloan", "Limasawa", "Maasin City (Capital)", "Macrohon", "Malitbog", "Padre Burgos", "Pintuyan", "Saint Bernard", "San Francisco", "San Juan (Cabalian)", "San Ricardo", "Silago", "Sogod", "Tomas Oppus"),
    "Sultan Kudarat" => array("Bagumbayan", "Columbio", "Esperanza", "Isulan (Capital)", "Kalamansig", "Lambayong (Mariano Marcos)", "Lebak", "Lutayan", "Palimbang", "President Quirino", "Senator Ninoy Aquino", "Tacurong City"),
    "Sulu" => array("Banguingui (Tongkil)", "Hadji Panglima Tahil (Marunggas)", "Indanan", "Jolo (Capital)", "Kalingalan Caluang", "Lugus", "Luuk", "Maimbung", "Old Panamao", "Omar", "Pandami", "Panglima Estino (New Panamao)", "Pangutaran", "Parang", "Pata", "Patikul", "Siasi", "Talipao", "Tapul"),
    "Surigao del Norte" => array("Alegria", "Bacuag", "Burgos", "Claver", "Dapa", "Del Carmen", "General Luna", "Gigaquit", "Mainit", "Malimono", "Pilar", "Placer", "San Benito", "San Francisco (Anao-aon)", "San Isidro", "Santa Monica (Sapao)", "Sison", "Socorro", "Surigao City (Capital)", "Tagana-an", "Tubod"),
    "Surigao del Sur" => array("Barobo", "Bayabas", "Bislig City", "Cagwait", "Cantilan", "Carmen", "Carrascal", "Cortes", "Hinatuan", "Lanuza", "Lianga", "Lingig", "Madrid", "Marihatag", "San Agustin", "San Miguel", "Tagbina", "Tago", "Tandag City (Capital)"),
    "Tarlac" => array("Anao", "Bamban", "Camiling", "Capas", "Concepcion", "Gerona", "La Paz", "Mayantoc", "Moncada", "Paniqui", "Pura", "Ramos", "San Clemente", "San Jose", "San Manuel", "Santa Ignacia", "Tarlac City (Capital)", "Victoria"),
    "Tawi-Tawi" => array("Bongao (Capital)", "Languyan", "Mapun (Cagayan de Tawi-Tawi)", "Panglima Sugala (Balimbing)", "Sapa-Sapa", "Sibutu", "Simunul", "Sitangkai", "South Ubian", "Tandubas", "Turtle Islands (Taganak)"),
    "Zambales" => array("Botolan", "Cabangan", "Candelaria", "Castillejos", "Iba (Capital)", "Masinloc", "Olongapo City", "Palauig", "San Antonio", "San Felipe", "San Marcelino", "San Narciso", "Santa Cruz", "Subic"),
    "Zamboanga del Norte" => array("Baliguian", "Dapitan City", "Dipolog City (Capital)", "Godod", "Gutalac", "Jose Dalman (Ponot)", "Kalawit", "Katipunan", "La Libertad", "Labason", "Leon B. Postigo (Bacungan)", "Liloy", "Manukan", "Mutia", "Piñan (New Piñan)", "Polanco", "Pres. Manuel A. Roxas", "Rizal", "Salug", "Sergio Osmeña Sr.", "Siayan", "Sibuco", "Sibutad", "Sindangan", "Siocon", "Sirawai", "Tampilisan"),
    "Zamboanga del Sur" => array("Aurora", "Bayog", "Dimataling", "Dinas", "Dumalinao", "Dumingag", "Guipos", "Josefina", "Kumalarang", "Labangan", "Lakewood", "Lapuyan", "Mahayag", "Margosatubig", "Midsalip", "Molave", "Pagadian City (Capital)", "Pitogo", "Ramon Magsaysay (Liargo)", "San Miguel", "San Pablo", "Sominot", "Tabina", "Tambulig", "Tigbao", "Tukuran", "Vincenzo A. Sagun"),
    "Zamboanga Sibugay" => array("Alicia", "Buug", "Diplahan", "Imelda", "Ipil (Capital)", "Kabasalan", "Mabuhay", "Malangas", "Naga", "Olutanga", "Payao", "Roseller Lim", "Siay", "Talusan", "Titay", "Tungawan")
  );
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="img/Favicon 2.png" type="image/png">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <link rel='stylesheet' href='https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css'>
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="css/hamburgers.css" rel="stylesheet">
  <link href="css/userdashboard.css" rel="stylesheet">
  <title>Edit Patient</title>
<style>
  .red{
    color:red;
  }
  .error-message{
    color:red;
    font-size: 11px;
    font-weight: bold;
  }
  .error-border {
  border: 1px solid red !important;
}

  </style>
 <script>
    function calculateAge() {
      const birthDate = document.getElementById('birthDate').value;
      const ageField = document.getElementById('age');
      if (birthDate) {
        const birthDateObj = new Date(birthDate);
        const today = new Date();
        let age = today.getFullYear() - birthDateObj.getFullYear();
        const monthDiff = today.getMonth() - birthDateObj.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDateObj.getDate())) {
          age--;
        }
        ageField.value = age;
      } else {
        ageField.value = '';
      }
    }
  </script>
</head>
<body>
<div class="container-fluid">
    <div class="main-container">
        <!-- Header and Sidebar -->
        <?php include 'includes/admin_header.php'; ?>
        <div class="sidebar">
            <?php include 'includes/sidebar.php'; ?>
        </div>


<!--Profile Picture and Details-->
        <div class="content" id="content">
    <div class="row justify-content-center mb-5 mt-4">

        <div class="col-md-11"> 
            <div class="card" style="background-color: #f5f5f5;">
                <div class="card-header card-header-patient-form text-center" style="background-color: #5E6E82;">
                    <h3> <b>EDIT PATIENT</b> <h3>
                </div>
                <div class="card-body px-5">
               

                



<form id="multi-step-form" enctype="multipart/form-data">
  <div class="step active" id="step1">
    <div class="row justify-content-center mt-3 px-3 ">
    <input type="hidden" name="patientID" value="<?php echo $patientID; ?>">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="fName">First Name</label>
        <input type="text" id="fName" name="fName" placeholder="First Name" value="<?php echo $firstName; ?>" class="form-control" oninput="preventLeadingSpace(event)" >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="mName">Middle Name</label>
        <input type="text" id="mName" name="mName" placeholder="Middle Name" value="<?php echo $middleName; ?> "class="form-control" oninput="preventLeadingSpace(event)" >
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="lName">Last Name</label>
        <input type="text" id="lName" name="lName" placeholder="Last Name"class="form-control" value="<?php echo $lastName; ?>" oninput="preventLeadingSpace(event)" >
      </div>
    </div>
    <div class="row justify-content-center px-3">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="birthDate">Birth Date</label>
        <input type="date" id="birthDate" name="birthDate" placeholder="Birth Date" class="form-control" value='<?php echo $birthDate?>'  max="<?php echo date('Y-m-d', strtotime('-1 year -1 day')); ?>" onkeydown="return false" onchange="calculateAge()">

      </div>
  
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="age">Age</label>
        <input type="tel" id="age" name="age" placeholder="Age" value="<?php echo $age; ?>" class="form-control" readonly>
      </div>
   
      <div class="col-lg-4 form-group px-4 mb-3">
    <label for="sex">Sex</label>
    <select id="sex" name="sex" class="form-control">
        <option value="Male" <?php echo ($sex == 'Male') ? 'selected' : ''; ?>>Male</option>
        <option value="Female" <?php echo ($sex == 'Female') ? 'selected' : ''; ?>>Female</option>
        <option value="Other" <?php echo ($sex == 'Other') ? 'selected' : ''; ?>>Other</option>
    </select>
</div>

    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="weight">Weight (kg)</label>
        <input type="tel" id="weight" name="weight" placeholder="Weight (kg)" value="<?php echo $weight; ?>" class="form-control">
      </div>
         <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="phoneNumber">Phone Number</label>
                                <div class="input-group">
                                    <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" placeholder="09123456789" style="min-width: 140px" value="<?php echo $contactNumber; ?>" maxlength="11" >
                                    <small id="phone-number-error" class="error-message"></small>
                                   <div>

                                   </div>
                                </div>
                            
                            </div>
                            
      <div class="col-lg-4 form-group px-4 mb-3">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Email Address" class="form-control" value="<?php echo $emailAddress; ?>" >
        <small id="emailError" class="error-message text-nowrap"></small>
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-0">
      <div class="col-lg-4 form-group px-4 mb-3">

      <label for="province">Province<span class="red">*</span></label>
      <select id="provinceSelect" name="province" class="form-control" required >
      <?php foreach ($provincesAndCities as $province => $cities): ?>
        <option value="<?php echo $province; ?>" <?php if ($province == $currentProvince) echo 'selected'; ?>>
            <?php echo $province; ?>
        </option>
    <?php endforeach; ?>
</select>
</div>
<div class="col-lg-4 form-group px-4 mb-3">
    <label for="citySelect">City<span class="red">*</span></label>
    <select id="citySelect" name="city" class="form-control" required>
        <option value="">Select City</option>
        <?php if (!empty($currentProvince)): ?>
            <?php foreach ($provincesAndCities[$currentProvince] as $city): ?>
                <option value="<?php echo $city; ?>" <?php if ($city == $currentCity) echo 'selected'; ?>>
                    <?php echo $city; ?>
                </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
</div>
      <div class="col-lg-4 form-group px-4 mb-3">
      <label for="address">Address</label>
        <input type="text" id="address" name="address" class="form-control" placeholder="Address" value="<?php echo $address; ?>"  oninput="preventLeadingSpace(event)" maxlength="150">
      </div>
    </div>
    <div class="row justify-content-center px-3 mb-3">
      <div class="col-lg-4 form-group px-4">
        <label for="emergencyContact">In case of Emergency, notify</label>
        <input type="text" id="emergencyContact" name="emergencyContact" placeholder="Full Name" class="form-control" value="<?php echo $emergencyContactName; ?>"   oninput="preventLeadingSpace(event)" maxlength="150">
      </div>
      <div class="col-lg-4 form-group px-4 mb-3">
    <label for="relationship">Relationship<span class="red">*</span></label>
    <select id="emergencyContactRelationship" name="emergency_contact_relationship" class="form-control" required>
        <option value="">Select Relationship</option>
        <option value="Spouse" <?php echo ($emergencyContactRelationship == 'Spouse') ? 'selected' : ''; ?>>Spouse</option>
        <option value="Parent" <?php echo ($emergencyContactRelationship == 'Parent') ? 'selected' : ''; ?>>Parent</option>
        <option value="Child" <?php echo ($emergencyContactRelationship == 'Child') ? 'selected' : ''; ?>>Child</option>
        <option value="Sibling" <?php echo ($emergencyContactRelationship == 'Sibling') ? 'selected' : ''; ?>>Sibling</option>
        <option value="Other Family Member" <?php echo ($emergencyContactRelationship == 'Other Family Member') ? 'selected' : ''; ?>>Other Family Member</option>
        <option value="Friend" <?php echo ($emergencyContactRelationship == 'Friend') ? 'selected' : ''; ?>>Friend</option>
        <option value="Colleague" <?php echo ($emergencyContactRelationship == 'Colleague') ? 'selected' : ''; ?>>Colleague</option>
        <option value="Neighbor" <?php echo ($emergencyContactRelationship == 'Neighbor') ? 'selected' : ''; ?>>Neighbor</option>
        <option value="Guardian" <?php echo ($emergencyContactRelationship == 'Guardian') ? 'selected' : ''; ?>>Guardian</option>
        <option value="Legal Representative" <?php echo ($emergencyContactRelationship == 'Legal Representative') ? 'selected' : ''; ?>>Legal Representative</option>
        <option value="Partner" <?php echo ($emergencyContactRelationship == 'Partner') ? 'selected' : ''; ?>>Partner</option>
        <option value="Caretaker" <?php echo ($emergencyContactRelationship == 'Caretaker') ? 'selected' : ''; ?>>Caretaker</option>
        <option value="Doctor" <?php echo ($emergencyContactRelationship == 'Doctor') ? 'selected' : ''; ?>>Doctor</option>
        <option value="Other" <?php echo ($emergencyContactRelationship == 'Other') ? 'selected' : ''; ?>>Other</option>
    </select>
</div>
      <div class="col-lg-4 form-group px-4 mb-3">
                                <label for="emergencyPhoneNumber">Phone Number</label>
                                <div class="input-group">

                                    <input type="tel" id="emergencyPhoneNumber" name="emergencyPhoneNumber" class="form-control" placeholder="09123456789" value="<?php echo $emergencyContactNumber?> " style="min-width: 140px" maxlength="11">
                                    <small id="emergency-phone-number-error" class="error-message"></small>
                                   <div>

                                   </div>
                                </div>
                            
                            </div>
    <div class="row justify-content-center">
    <button type="submit" id="submit-button" class="btn-customized" style="background-color: #5E6E82;">Edit</button>
</div>
  </div>
  </div>


  </form>
</div>
</div>
</div>  
</div>
</div>










                       
    
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    
    <!-- Data Table JS -->
<script src='https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js'></script>
<script src='https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js'></script>
    <script src="https://cdn.datatables.net/buttons/2.0.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.0.0/js/buttons.html5.min.js"></script>


 
  
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  
  <script>
    // Define JavaScript variable to hold PHP-generated data
var provincesAndCities = <?php echo json_encode($provincesAndCities); ?>;

// Get references to the province and city select elements
var provinceSelect = document.getElementById("provinceSelect");
var citySelect = document.getElementById("citySelect");

// Add event listener to the province select element
provinceSelect.addEventListener("change", function() {
    // Clear previous options in the city select element
    citySelect.innerHTML = "<option value=''>Select City</option>";
    
    // Get the selected province
    var selectedProvince = this.value;
    
    // Get the cities for the selected province from JavaScript variable
    var cities = provincesAndCities[selectedProvince];
    
    // Add options for cities to the city select element
    if (cities) {
        cities.forEach(function(city) {
            var option = document.createElement("option");
            option.value = city;
            option.textContent = city;
            citySelect.appendChild(option);
        });
    }
});
  </script>
  <script>
  document.getElementById("profileDropdown").addEventListener("mousedown", function(event) {
    event.preventDefault(); // Prevent the default action of the mousedown event
    var dropdownContent = document.getElementById("dropdownContent");

    // Check if the clicked element is within the dropdown content
    if (!dropdownContent.contains(event.target)) {
        // Clicked outside the dropdown content, toggle its visibility
        if (dropdownContent.style.display === "block") {
            dropdownContent.style.display = "none";
        } else {
            dropdownContent.style.display = "block";
        }
    }
});
</script>


<script>
  // Select all number input fields
const numberInputs = document.querySelectorAll('input[type="number"]');

// Loop through each number input field
numberInputs.forEach(function(input) {
    // Add an event listener for the input event
    input.addEventListener('input', function() {
        // Get the current value of the input field
        let value = parseFloat(this.value);

        // If the value is negative, set it to 0
        if (value < 0 || isNaN(value)) {
            this.value = 0;
        }
    });
});

  // Get the weight input element
const weightInput = document.getElementById('weight');

// Add an event listener for the keydown event
weightInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});

// Get the email input element
const emailInput = document.getElementById('email');

// Add an event listener for the keydown event
emailInput.addEventListener('keydown', function(event) {
    // Get the key code of the pressed key
    const keyCode = event.keyCode;

    // If the pressed key is the spacebar, prevent the default behavior
    if (keyCode === 32) {
        event.preventDefault();
    }
});

// Get the age input element
const ageInput = document.getElementById('age');

// Add an event listener for the keydown event
ageInput.addEventListener('keydown', function(event) {
    // Get the key code of the pressed key
    const keyCode = event.keyCode;

    // Allow special keys like backspace, delete, arrow keys, etc.
    if (
        // Allow: backspace, delete, tab, escape, enter
        [8, 9, 27, 13].includes(keyCode) ||
        // Allow: Ctrl+A
        (keyCode === 65 && event.ctrlKey === true) ||
        // Allow: home, end, left, right, down, up
        (keyCode >= 35 && keyCode <= 40)
    ) {
        // Let it happen, don't do anything
        return;
    }

    // Ensure that it is a number and stop the keypress if it isn't
    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});
// Get the phoneNumber and emergencyPhoneNumber input elements
const phoneNumberInput = document.getElementById('phoneNumber');
const emergencyPhoneNumberInput = document.getElementById('emergencyPhoneNumber');

// Add an event listener for the keydown event on phoneNumber input
phoneNumberInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});

// Add an event listener for the keydown event on emergencyPhoneNumber input
emergencyPhoneNumberInput.addEventListener('keydown', function(event) {
    const keyCode = event.keyCode;

    if (
        [8, 9, 27, 13].includes(keyCode) || // Allow: backspace, delete, tab, escape, enter
        (keyCode === 65 && event.ctrlKey === true) || // Allow: Ctrl+A
        (keyCode >= 35 && keyCode <= 40) // Allow: home, end, left, right, down, up
    ) {
        return;
    }

    if ((event.shiftKey || (keyCode < 48 || keyCode > 57)) && (keyCode < 96 || keyCode > 105)) {
        event.preventDefault();
    }
});







 // Get all sidebar items
const sidebarItems = document.querySelectorAll('.sidebar-item');

// Loop through each sidebar item
sidebarItems.forEach(function(sidebarItem) {
    // Find the icon within the sidebar item
    const sidebarIcon = sidebarItem.querySelector('.sidebar-icon');

    // Get the paths to the default and hover icons from data attributes
    const defaultIcon = sidebarItem.dataset.defaultIcon;
    const hoverIcon = sidebarItem.dataset.hoverIcon;

    // Add mouseenter event listener
    sidebarItem.addEventListener('mouseenter', function() {
        // Change the source of the icon to the hover icon upon hover
        sidebarIcon.src = hoverIcon;
    });

    // Add mouseleave event listener
    sidebarItem.addEventListener('mouseleave', function() {
        // Change the source of the icon back to the default icon upon mouse leave
        sidebarIcon.src = defaultIcon;
    });
});

$(document).ready(function () {
    $('#sidebarCollapse1').on('click', function () {
        $('#sidebar').toggleClass('collapsed'); // Toggle 'collapsed' class on #sidebar
        $('#content').toggleClass('collapsed'); // Toggle 'collapsed' class on #content
    });
});




document.getElementById('submit-button').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    // Validate the form before submitting
    if (validateForm()) {
        // If no errors, proceed with form submission using AJAX
        const formData = new FormData(document.getElementById('multi-step-form'));

        $.ajax({
            url: 'backend/edit-existing.php', // Replace with your actual endpoint
            method: 'POST',
            data: formData,
            contentType: false, // Important for file uploads
            processData: false, // Important for file uploads
            success: function(response) {
                // Handle success response
                console.log(response);
                // Optionally, you can reset the form after successful submission
                window.location.href = 'patient-list.php'; // Replace with your actual URL
            },
            error: function(xhr, status, error) {
                // Handle error response
                console.error(error);
                // Optionally, display an error message to the user
                alert('An error occurred while submitting the form. Please try again later.');
            }
        });
    } else {
        // If there are validation errors, prevent form submission
        console.log('Please fill in all required fields correctly.');
    }
});


function validateForm() {
    let isValid = true;

    // Validate each form field here
    const phoneNumberField = document.getElementById('phoneNumber');
    const emergencyPhoneNumberField = document.getElementById('emergencyPhoneNumber');
    const emailField = document.getElementById('email');

    // Validate phone number format
    if (!validatePhoneNumber(phoneNumberField)) {
        isValid = false;
        document.getElementById('phone-number-error').textContent = 'Phone number must be exactly 11 digits.';
    } else {
        document.getElementById('phone-number-error').textContent = '';
    }

    // Validate emergency phone number format
    if (!validatePhoneNumber(emergencyPhoneNumberField)) {
        isValid = false;
        document.getElementById('emergency-phone-number-error').textContent = 'Emergency phone number must be exactly 11 digits.';
    } else {
        document.getElementById('emergency-phone-number-error').textContent = '';
    }

 // Validate email domain (if email is provided)
if (emailField.value.trim() !== '' && !validateEmailDomain(emailField.value)) {
    isValid = false;
    emailField.setCustomValidity('Email must be from outlook.com, yahoo.com, or gmail.com');
    emailError.textContent = 'Please apply valid email(gmail,outlook,yahoo)'; // Display error message
} else {
    emailField.setCustomValidity('');
    emailError.textContent = ''; // Clear error message
}

    // Additional validations for other fields can be added here

    return isValid;
}

// Function to validate phone number format
function validatePhoneNumber(inputField) {
    const phoneNumber = inputField.value.trim();
    // Check if phone number is exactly 11 digits
    return /^\d{11}$/.test(phoneNumber);
}

// Function to validate email domain
function validateEmailDomain(email) {
    const allowedDomains = ['outlook.com', 'yahoo.com', 'gmail.com'];
    const domain = email.split('@')[1];
    return allowedDomains.includes(domain);
}



</script>
<script>
function preventLeadingSpace(event) {
    const input = event.target;
    if (input.value.startsWith(' ')) {
        input.value = input.value.trim(); // Remove leading space
    }
    // Replace multiple consecutive spaces with a single space
    input.value = input.value.replace(/\s{2,}/g, ' ');
}

function preventSpaces(event) {
        const input = event.target;
        if (input.value.includes(' ')) {
            input.value = input.value.replace(/\s/g, ''); // Remove all spaces
        }
    }




</script>
<!-- Existing JavaScript and closing body tag -->


</body>
</html>

<div class="space-y-4">
    <div class="grid items-start gap-4 xl:grid-cols-2">
        <div class="space-y-4">
            <div class="card space-y-2 text-sm">
                <div class="font-semibold">Cara menghubungkan perangkat RFID</div>
                <p class="text-slate-600">Kirim <code>POST</code> ke endpoint berikut dengan header <code>X-Device-Key</code> berisi API key perangkat:</p>
                <pre class="overflow-x-auto rounded-lg bg-slate-900 p-3 text-xs text-slate-100">curl -X POST {{ $endpoint }} \
  -H "X-Device-Key: API_KEY_PERANGKAT" \
  -H "Accept: application/json" \
  -d "uid=04A1B2C3"</pre>
            </div>

            <div class="card space-y-4 text-sm">
        <div>
            <div class="font-semibold">Panduan ESP32 + RFID + LCD I2C 16x2 + buzzer</div>
            <p class="mt-1 text-slate-600">Gunakan alamat IP server Laravel di jaringan yang sama dengan ESP32. Jangan gunakan <code>localhost</code> pada ESP32.</p>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <div>
                <div class="mb-2 font-medium">Wiring</div>
                <div class="overflow-x-auto rounded-lg ring-1 ring-slate-200">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-500">
                            <tr><th class="px-3 py-2">Komponen</th><th class="px-3 py-2">Pin</th><th class="px-3 py-2">ESP32</th></tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <tr><td class="px-3 py-2">RC522</td><td class="px-3 py-2">SDA / SS</td><td class="px-3 py-2">GPIO 5</td></tr>
                            <tr><td class="px-3 py-2">RC522</td><td class="px-3 py-2">SCK, MOSI, MISO</td><td class="px-3 py-2">18, 23, 19</td></tr>
                            <tr><td class="px-3 py-2">RC522</td><td class="px-3 py-2">RST</td><td class="px-3 py-2">GPIO 27</td></tr>
                            <tr><td class="px-3 py-2">RC522</td><td class="px-3 py-2">3.3V / GND</td><td class="px-3 py-2">3V3 / GND</td></tr>
                            <tr><td class="px-3 py-2">LCD I2C</td><td class="px-3 py-2">SDA / SCL</td><td class="px-3 py-2">21 / 22</td></tr>
                            <tr><td class="px-3 py-2">Buzzer</td><td class="px-3 py-2">+ / -</td><td class="px-3 py-2">GPIO 4 / GND</td></tr>
                        </tbody>
                    </table>
                </div>
                <p class="mt-2 text-xs text-slate-500">LCD biasanya beralamat <code>0x27</code>; coba <code>0x3F</code> jika layar tidak tampil. RC522 wajib 3.3V.</p>
            </div>

            <div>
                <div class="mb-2 font-medium">Sebelum upload</div>
                <ol class="list-inside list-decimal space-y-1 text-slate-600">
                    <li>Install library <code>MFRC522</code>, <code>LiquidCrystal_I2C</code>, dan <code>ArduinoJson</code>.</li>
                    <li>Buat perangkat di halaman ini, lalu salin API key-nya.</li>
                    <li>Isi <code>API_KEY</code> dan <code>SERVER_URL</code> pada sketch, lalu upload ke ESP32.</li>
                    <li>Sambungkan HP/PC ke WiFi <code>ESP32-RFID-SETUP</code> (password <code>rfidsetup</code>), buka <code>192.168.4.1</code>, dan isi SSID/password WiFi.</li>
                    <li>Tekan <strong>Uji &amp; Simpan</strong>. Kredensial hanya disimpan jika koneksi berhasil.</li>
                    <li>Daftarkan UID kartu pada menu <strong>Kartu RFID</strong>.</li>
                </ol>
            </div>
        </div>

        <div x-data="{ expanded: false, copied: false, async copyCode() {
                try {
                    await navigator.clipboard.writeText(this.$refs.esp32Code.value);
                    this.copied = true;
                    setTimeout(() => this.copied = false, 1800);
                } catch (error) {
                    this.$refs.esp32Code.select();
                    document.execCommand('copy');
                    this.copied = true;
                    setTimeout(() => this.copied = false, 1800);
                }
            } }">
            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                <div class="font-medium">Sketch Arduino ESP32</div>
                <div class="flex gap-2">
                    <button type="button" class="btn btn-ghost text-xs" x-on:click="copyCode()" x-text="copied ? 'Tersalin' : 'Salin kode'"></button>
                    <button type="button" class="btn btn-ghost text-xs" x-on:click="expanded = !expanded" x-text="expanded ? 'Ringkas' : 'Buka kode'"></button>
                </div>
            </div>
            <textarea x-ref="esp32Code" readonly rows="8" x-bind:class="expanded ? 'h-[38rem]' : 'h-32'" class="w-full resize-y overflow-auto rounded-lg bg-slate-900 p-3 font-mono text-xs leading-relaxed text-slate-100 transition-[height] duration-200">#include &lt;WiFi.h&gt;
            #include &lt;WebServer.h&gt;
            #include &lt;Preferences.h&gt;
            #include &lt;HTTPClient.h&gt;
            #include &lt;ArduinoJson.h&gt;
            #include &lt;SPI.h&gt;
            #include &lt;MFRC522.h&gt;
            #include &lt;Wire.h&gt;
            #include &lt;LiquidCrystal_I2C.h&gt;

const char* API_KEY = "API_KEY_PERANGKAT";
const char* SERVER_URL = "{{ $endpoint }}";
            const char* SETUP_SSID = "ESP32-RFID-SETUP";
            const char* SETUP_PASSWORD = "rfidsetup";

            constexpr byte RFID_SS_PIN = 5;
            constexpr byte RFID_RST_PIN = 27;
            constexpr byte BUZZER_PIN = 4;
            constexpr uint8_t LCD_ADDRESS = 0x27;

            MFRC522 rfid(RFID_SS_PIN, RFID_RST_PIN);
            LiquidCrystal_I2C lcd(LCD_ADDRESS, 16, 2);
            WebServer server(80);
            Preferences preferences;
            bool setupMode = false;

void showLcd(const char* line1, const char* line2) {
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(line1);
  lcd.setCursor(0, 1);
  lcd.print(line2);
}

void beep(uint16_t duration = 120, uint8_t count = 1) {
  for (uint8_t i = 0; i &lt; count; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(duration);
    digitalWrite(BUZZER_PIN, LOW);
    if (i + 1 &lt; count) delay(80);
  }
}

String uidToString() {
  String uid;
  for (byte i = 0; i &lt; rfid.uid.size; i++) {
    if (rfid.uid.uidByte[i] &lt; 0x10) uid += "0";
    uid += String(rfid.uid.uidByte[i], HEX);
  }
  uid.toUpperCase();
  return uid;
}

String setupPage(const String& message = "") {
  String page = R"rawliteral(
<!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Setup WiFi RFID</title><style>
body{font-family:Arial;max-width:420px;margin:40px auto;padding:20px}
input,button{box-sizing:border-box;width:100%;padding:12px;margin:6px 0}
button{background:#4f46e5;color:white;border:0;border-radius:6px}
.msg{padding:10px;background:#eef2ff}
</style></head><body><h2>Setup WiFi ESP32</h2>)rawliteral";
  if (message.length() &gt; 0) page += "&lt;p class='msg'&gt;" + message + "&lt;/p&gt;";
  page += R"rawliteral(
<form method="post" action="/save">
<label>SSID WiFi</label><input name="ssid" required>
<label>Password WiFi</label><input name="password" type="password">
<button>Uji &amp; Simpan</button></form></body></html>)rawliteral";
  return page;
}

void handleSetup() {
  server.send(200, "text/html", setupPage());
}

void handleSaveWifi() {
  String ssid = server.arg("ssid");
  String password = server.arg("password");
  ssid.trim();

  if (ssid.length() == 0) {
    server.send(400, "text/html", setupPage("SSID wajib diisi."));
    return;
  }

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid.c_str(), password.c_str());
  showLcd("UJI WIFI", "TUNGGU...");
  unsigned long startedAt = millis();
  while (WiFi.status() != WL_CONNECTED &amp;&amp; millis() - startedAt &lt; 15000) {
    delay(250);
  }

  if (WiFi.status() != WL_CONNECTED) {
    WiFi.disconnect(true);
    WiFi.mode(WIFI_AP);
    WiFi.softAP(SETUP_SSID, SETUP_PASSWORD);
    showLcd("WIFI GAGAL", "COBA LAGI");
    server.send(200, "text/html", setupPage("Koneksi gagal. Periksa SSID/password lalu coba lagi."));
    return;
  }

  preferences.begin("wifi", false);
  preferences.putString("ssid", ssid);
  preferences.putString("password", password);
  preferences.end();
  showLcd("WIFI BERHASIL", WiFi.localIP().toString().c_str());
  server.send(200, "text/html", setupPage("Berhasil! SSID dan password sudah disimpan. ESP32 akan mulai dalam 3 detik."));
  delay(3000);
  server.stop();
  WiFi.softAPdisconnect(true);
  setupMode = false;
}

bool connectSavedWifi() {
  preferences.begin("wifi", true);
  String ssid = preferences.getString("ssid", "");
  String password = preferences.getString("password", "");
  preferences.end();
  if (ssid.length() == 0) return false;

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid.c_str(), password.c_str());
  showLcd("SAMBUNG WIFI", "TUNGGU...");
  unsigned long startedAt = millis();
  while (WiFi.status() != WL_CONNECTED &amp;&amp; millis() - startedAt &lt; 10000) {
    delay(250);
  }
  return WiFi.status() == WL_CONNECTED;
}

void startSetupPortal() {
  setupMode = true;
  WiFi.mode(WIFI_AP);
  WiFi.softAP(SETUP_SSID, SETUP_PASSWORD);
  server.on("/", HTTP_GET, handleSetup);
  server.on("/save", HTTP_POST, handleSaveWifi);
  server.begin();
  showLcd("HUBUNGKAN WIFI", "192.168.4.1");
}

void connectWifi() {
  if (connectSavedWifi()) {
    showLcd("SIAP SCAN", WiFi.localIP().toString().c_str());
    delay(1500);
    return;
  }
  startSetupPortal();
}

void setup() {
  Serial.begin(115200);
  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);
  Wire.begin(21, 22);
  lcd.init();
  lcd.backlight();
  SPI.begin();
  rfid.PCD_Init();
  connectWifi();
}

void sendAttendance(const String& uid) {
  if (WiFi.status() != WL_CONNECTED) connectWifi();

  HTTPClient http;
  http.begin(SERVER_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.addHeader("X-Device-Key", API_KEY);
  int statusCode = http.POST("uid=" + uid);
  String response = http.getString();
  http.end();

  StaticJsonDocument&lt;512&gt; document;
  DeserializationError error = deserializeJson(document, response);
  if (error) {
    showLcd("RESPON ERROR", String(statusCode).c_str());
    beep(300, 2);
    delay(2000);
    return;
  }

  const char* line1 = document["lcd"]["line1"] | "RESPON ERROR";
  const char* line2 = document["lcd"]["line2"] | "COBA LAGI";
  showLcd(line1, line2);
  beep(statusCode == 200 ? 100 : 300, statusCode == 200 ? 1 : 2);
  delay(2500);
  showLcd("SIAP SCAN", "");
}

void loop() {
  if (setupMode) {
    server.handleClient();
    delay(2);
    return;
  }

  if (!rfid.PICC_IsNewCardPresent() || !rfid.PICC_ReadCardSerial()) {
    delay(50);
    return;
  }

  sendAttendance(uidToString());
  rfid.PICC_HaltA();
  rfid.PCD_StopCrypto1();
}</textarea>
        </div>
            </div>
        </div>

        <div class="space-y-4">
            <div class="flex justify-end"><button wire:click="create" class="btn btn-primary">+ Tambah perangkat</button></div>

            <div class="space-y-3">
                @forelse ($devices as $d)
                    <div class="card" wire:key="dev-{{ $d->id }}" x-data="{ show: false }">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <div class="font-semibold">{{ $d->name }}
                            <span class="badge ml-2 {{ $d->is_active ? ($d->is_online ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500') : 'bg-red-100 text-red-700' }}">
                                {{ $d->is_active ? ($d->is_online ? 'Online' : 'Offline') : 'Nonaktif' }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-500">{{ $d->location ?? '-' }} · terakhir terlihat {{ $d->last_seen_at?->diffForHumans() ?? 'belum pernah' }}</div>
                    </div>
                    <div class="space-x-1">
                        <button wire:click="toggle({{ $d->id }})" class="btn btn-ghost">{{ $d->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                        <button wire:click="edit({{ $d->id }})" class="btn btn-ghost">Ubah</button>
                        <button wire:click="delete({{ $d->id }})" wire:confirm="Hapus perangkat ini?" class="btn btn-danger">Hapus</button>
                    </div>
                </div>
                <div class="mt-3 flex flex-wrap items-center gap-2 text-sm">
                    <span class="text-slate-500">API key:</span>
                    <code class="rounded bg-slate-100 px-2 py-1 text-xs" x-text="show ? '{{ $d->api_key }}' : '••••••••••••••••••••'"></code>
                    <button type="button" class="text-xs text-indigo-600 hover:underline" x-on:click="show = !show" x-text="show ? 'Sembunyikan' : 'Tampilkan'"></button>
                    <button type="button" class="text-xs text-indigo-600 hover:underline" x-on:click="navigator.clipboard.writeText('{{ $d->api_key }}')">Salin</button>
                    <button type="button" wire:click="regenerate({{ $d->id }})" wire:confirm="Buat API key baru? Key lama langsung tidak berlaku." class="text-xs text-red-600 hover:underline">Buat ulang</button>
                </div>
                    </div>
                @empty
                    <div class="card text-center text-sm text-slate-400">Belum ada perangkat.</div>
                @endforelse
            </div>
        </div>
    </div>

    @if ($showForm)
        <div class="fixed inset-0 z-10 flex items-center justify-center bg-black/40 p-4">
            <form wire:submit="save" class="card w-full max-w-md space-y-4">
                <h2 class="text-lg font-semibold">{{ $editingId ? 'Ubah perangkat' : 'Tambah perangkat' }}</h2>
                <div><label class="label">Nama</label><input wire:model="name" class="input">@error('name')<p class="err">{{ $message }}</p>@enderror</div>
                <div><label class="label">Lokasi</label><input wire:model="location" class="input">@error('location')<p class="err">{{ $message }}</p>@enderror</div>
                <div class="flex justify-end gap-2">
                    <button type="button" wire:click="$set('showForm', false)" class="btn btn-ghost">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    @endif
</div>

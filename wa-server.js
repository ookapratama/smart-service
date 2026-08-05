import express from 'express';
import pkg from 'whatsapp-web.js';
import qrcode from 'qrcode-terminal';
import fs from 'fs';

const { Client, LocalAuth, Buttons } = pkg;

const app = express();
const PORT = process.env.PORT || 3000;
const API_SECRET = process.env.WA_WEB_JS_SECRET || '';

app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Detect local Chrome/Edge executable on Windows to bypass Puppeteer download issues
function getSystemChromePath() {
    if (process.env.PUPPETEER_EXECUTABLE_PATH) {
        return process.env.PUPPETEER_EXECUTABLE_PATH;
    }
    const knownPaths = [
        'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
        'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
        'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
        'C:\\Program Files\\BraveSoftware\\Brave-Browser\\Application\\brave.exe',
    ];
    for (const browserPath of knownPaths) {
        if (fs.existsSync(browserPath)) {
            return browserPath;
        }
    }
    return undefined;
}

// Express Middleware Auth (optional)
app.use((req, res, next) => {
    if (req.path === '/status' || req.path === '/qr') {
        return next();
    }
    if (!API_SECRET) {
        return next();
    }
    const authHeader = req.headers['authorization'] || '';
    const apiKeyHeader = req.headers['x-api-key'] || '';
    const token = authHeader.replace('Bearer ', '').trim();

    if (token === API_SECRET || apiKeyHeader === API_SECRET) {
        return next();
    }
    return res.status(401).json({ status: false, message: 'Unauthorized API Key' });
});

let isReady = false;
let currentQr = '';

console.log('[WA-SERVER] Initializing whatsapp-web.js client...');

const chromePath = getSystemChromePath();
if (chromePath) {
    console.log(`[WA-SERVER] Menggunakan Browser Sistem: ${chromePath}`);
}

const client = new Client({
    authStrategy: new LocalAuth({
        clientId: 'smart-service-wa'
    }),
    puppeteer: {
        headless: true,
        executablePath: chromePath,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ]
    }
});

client.on('qr', (qr) => {
    currentQr = qr;
    isReady = false;
    console.log('\n[WA-SERVER] Scan QR Code berikut dengan WhatsApp HP Anda (atau buka http://localhost:3000/qr):');
    qrcode.generate(qr, { small: true });
});

client.on('ready', () => {
    isReady = true;
    currentQr = '';
    console.log('\n[WA-SERVER] WhatsApp Client SIAP dan Terhubung!');
});

client.on('authenticated', () => {
    console.log('[WA-SERVER] Otentikasi WhatsApp Berhasil!');
});

client.on('auth_failure', (msg) => {
    console.error('[WA-SERVER] Otentikasi Gagal:', msg);
    isReady = false;
});

client.on('disconnected', (reason) => {
    console.warn('[WA-SERVER] WhatsApp Terputus:', reason);
    isReady = false;
    client.initialize();
});

client.initialize();

// Route: Tampilkan Web Page QR Code di Browser
app.get('/qr', (req, res) => {
    if (isReady) {
        return res.send(`
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <title>WhatsApp Status</title>
                <style>
                    body { font-family: system-ui, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f4f6f8; }
                    .card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
                    .badge { display: inline-block; background: #22c55e; color: white; padding: 6px 16px; border-radius: 20px; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="card">
                    <h2>Smart Service WA Gateway</h2>
                    <div class="badge">✓ Terhubung & Siap Digunakan</div>
                    <p style="color: #666; margin-top: 1rem;">WhatsApp Web JS aktif dan terverifikasi.</p>
                </div>
            </body>
            </html>
        `);
    }

    if (!currentQr) {
        return res.send(`
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta http-equiv="refresh" content="3">
                <title>Memuat QR Code...</title>
                <style>
                    body { font-family: system-ui, sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #f4f6f8; }
                    .card { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center; }
                </style>
            </head>
            <body>
                <div class="card">
                    <h2>Menyiapkan QR Code...</h2>
                    <p>Sedang menghubungkan ke WhatsApp. Halaman ini akan otomatis memuat ulang dalam 3 detik...</p>
                </div>
            </body>
            </html>
        `);
    }

    const qrImageUrl = `https://api.qrserver.com/v1/create-qr-code/?data=${encodeURIComponent(currentQr)}&size=300x300`;

    return res.send(`
        <!DOCTYPE html>
        <html lang="id">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="refresh" content="10">
            <title>Scan QR Code WhatsApp</title>
            <style>
                body { font-family: system-ui, sans-serif; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 100vh; margin: 0; background: #0f172a; color: white; }
                .card { background: #1e293b; padding: 2.5rem; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.3); text-align: center; max-width: 400px; border: 1px solid #334155; }
                img { border-radius: 12px; border: 4px solid white; background: white; padding: 8px; }
                .sub { color: #94a3b8; font-size: 0.9rem; margin-top: 1rem; }
            </style>
        </head>
        <body>
            <div class="card">
                <h2 style="margin-top:0;">Scan QR Code WhatsApp</h2>
                <img src="${qrImageUrl}" alt="Scan QR Code" width="280" height="280">
                <p class="sub">Buka WhatsApp di HP Anda &rarr; Perangkat Tertaut &rarr; Tautkan Perangkat.<br><br><i>Halaman ini akan otomatis refresh.</i></p>
            </div>
        </body>
        </html>
    `);
});

// Route: Status Server & WA Session
app.get('/status', (req, res) => {
    return res.json({
        status: true,
        ready: isReady,
        qrAvailable: Boolean(currentQr),
        timestamp: new Date().toISOString()
    });
});

// Route: Send Message
app.post('/send-message', async (req, res) => {
    try {
        if (!isReady) {
            return res.status(503).json({
                status: false,
                message: 'WhatsApp Web Client belum siap/belum login. Silakan scan QR Code di terminal atau buka http://localhost:3000/qr.'
            });
        }

        const { phone, number, message, title, footer, buttons } = req.body;
        const targetNumber = phone || number;

        if (!targetNumber || !message) {
            return res.status(400).json({
                status: false,
                message: 'Parameter phone/number dan message wajib diisi.'
            });
        }

        let cleaned = String(targetNumber).replace(/[^0-9]/g, '');
        if (cleaned.startsWith('0')) {
            cleaned = '62' + cleaned.slice(1);
        }

        const chatId = cleaned.endsWith('@c.us') ? cleaned : `${cleaned}@c.us`;

        console.log(`\n=================== [WA-SERVER] ===================\nTO     : ${chatId}\nMESSAGE:\n${message}\n=====================================================\n`);

        let sentMsg;

        if (Array.isArray(buttons) && buttons.length > 0) {
            try {
                const formattedButtons = buttons.map((btn, index) => {
                    if (typeof btn === 'string') {
                        return { id: `btn_${index + 1}`, body: btn };
                    }
                    const text = btn.body || btn.label || btn.text || btn.displayText || `Tombol ${index + 1}`;
                    return {
                        id: btn.id || `btn_${index + 1}`,
                        body: String(text)
                    };
                });

                const buttonMsg = new Buttons(
                    message,
                    formattedButtons,
                    title || '',
                    footer || 'Pemerintah Kecamatan Soreang'
                );

                sentMsg = await client.sendMessage(chatId, buttonMsg);
            } catch (buttonErr) {
                console.warn('[WA-SERVER] Sending native buttons failed, falling back to text:', buttonErr.message);
                sentMsg = await client.sendMessage(chatId, message);
            }
        } else {
            sentMsg = await client.sendMessage(chatId, message);
        }

        return res.json({
            status: true,
            message: 'Pesan berhasil dikirim via whatsapp-web.js',
            id: sentMsg.id ? sentMsg.id.id : 'sent',
            to: chatId
        });
    } catch (err) {
        console.error('[WA-SERVER] Error sending message:', err);
        return res.status(500).json({
            status: false,
            message: 'Gagal mengirim pesan via whatsapp-web.js',
            error: err.message
        });
    }
});

app.listen(PORT, () => {
    console.log(`[WA-SERVER] Express Gateway Server berjalan pada http://127.0.0.1:${PORT}`);
    console.log(`[WA-SERVER] Buka http://localhost:${PORT}/qr di browser jika QR Code di terminal tidak terlihat.`);
});

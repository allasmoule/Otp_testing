# Mobile OTP Verification & Admin System (DianaHost / ZendSMS & Laravel)

A complete, production-grade **Mobile OTP Verification & Authentication System** built with **Laravel Framework**, **DianaHost / ZendSMS API**, and **SQLite / MySQL Database**. 

Features an enterprise-level security architecture, rate limiting, single-use cryptographically hashed OTP storage, Bangladesh phone number normalization, and a glassmorphic Admin Panel UI.

---

## 🌟 Key Features

- **📲 DianaHost & ZendSMS SMS Integration**: Native integration with DianaHost / ZendSMS SMS API via environment configuration (`DIANAHOST_API_KEY`, `DIANAHOST_SENDER_ID`).
- **🇧🇩 Bangladesh Phone Normalization**: Automatically standardizes inputs (`017...`, `88017...`, `+88017...`) into international format (`+8801XXXXXXXXX`) to eliminate duplicate user accounts.
- **🛡️ Cryptographically Secure OTP**: Generates random 6-digit codes (`random_int`) and stores them securely as **Bcrypt Hashes** in the database. Plain OTP is **NEVER** stored or logged.
- **⚡ Brute-Force & Attempt Protection**: Maximum 5 verification attempts per OTP. Automatically invalidates the code on the 6th wrong try.
- **⏳ Resend Cooldown & Hourly Limits**: Enforces a 60-second waiting period between resends and limits requests to 5 per hour per phone/IP.
- **🔑 Direct Password Login**: Once verified, users log in directly via mobile number & password without needing OTP again.
- **📊 Glassmorphic Admin Portal**: Protected `/admin` dashboard displaying account identity, verification badge, and one-click Logout.
- **🌐 Shared Hosting Ready**: Built for seamless deployment to Hostinger, cPanel, or traditional PHP shared hosting.

---

## 📂 Architecture & Important Files

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/OtpController.php       # /api/otp/send, /verify, /resend API
│   │   │   ├── AuthController.php          # Login, Register, Logout
│   │   │   └── AdminController.php         # Protected Admin Dashboard
│   │   └── Requests/
│   │       ├── SendOtpRequest.php
│   │       └── VerifyOtpRequest.php
│   ├── Models/
│   │   ├── OtpVerification.php             # Eloquent Model for OTP records
│   │   └── User.php                        # Authenticatable User Model
│   └── Services/
│       ├── DianaHostSmsService.php         # DianaHost SMS API Gateway Client
│       └── OtpService.php                  # Core OTP & Normalization Business Logic
├── config/
│   └── dianahost.php                       # DianaHost & OTP Security Config
├── database/
│   └── migrations/
│       └── 2026_09_15_100000_create_otp_verifications_table.php
├── resources/
│   └── views/
│       ├── auth/portal.blade.php           # Login & Multi-step OTP Signup UI
│       ├── admin/dashboard.blade.php      # Admin Panel View
│       └── layouts/app.blade.php           # Base Layout
└── routes/
    ├── api.php                             # /api/otp/* endpoints
    └── web.php                             # Web routes
```

---

## 🛠️ Local Environment Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/allasmoule/Otp_testing.git
   cd Otp_testing
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure `.env`**:
   Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Set DianaHost SMS Credentials in `.env`**:
   ```env
   DIANAHOST_API_KEY=your_dianahost_api_key
   DIANAHOST_SENDER_ID=8809612781000
   DIANAHOST_API_URL=https://api.zendsms.com/api/v1/send-sms

   # Set true for offline local simulation, false for live SMS
   OTP_TEST_MODE=false
   ```

5. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

6. **Serve the Application**:
   ```bash
   php -S localhost:8080 -t public
   # OR
   php artisan serve
   ```
   Open `http://localhost:8080` in your web browser.

---

## 📡 API Endpoints

### 1. Send OTP
- **URL**: `POST /api/otp/send`
- **Body**: `{ "phone": "01712345678" }`
- **Response**:
  ```json
  {
    "success": true,
    "message": "OTP sent successfully."
  }
  ```

### 2. Verify OTP
- **URL**: `POST /api/otp/verify`
- **Body**: `{ "phone": "01712345678", "otp": "123456" }`
- **Response**:
  ```json
  {
    "success": true,
    "message": "Phone number verified successfully."
  }
  ```

### 3. Resend OTP
- **URL**: `POST /api/otp/resend`
- **Body**: `{ "phone": "01712345678" }`

---

## 📦 Hostinger / cPanel Shared Hosting Deployment

1. **Upload Code**: Upload ZIP of the codebase (excluding `node_modules`, `vendor`, `.env`) to your cPanel File Manager.
2. **Setup Database**: Create MySQL database on cPanel and update `.env` credentials.
3. **Set Provider Key**: Set `DIANAHOST_API_KEY` and `DIANAHOST_SENDER_ID` in `.env`.
4. **Run Migrations & Cache**:
   ```bash
   php artisan migrate --force
   php artisan config:cache
   php artisan route:cache
   ```

---

## 📄 License
Licensed under the [MIT License](LICENSE).

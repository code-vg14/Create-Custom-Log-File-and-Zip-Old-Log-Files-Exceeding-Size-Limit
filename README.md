# Secure Custom Logging & Log Rotation Engine

A robust, thread-safe logging and automated archiving utility designed for CakePHP applications. This engine replaces traditional file logging approaches—such as the legacy `LoggingController.php` pattern—with a clean, decoupled architecture that integrates directly with CakePHP's core global logging system.

---

## 🛠️ Architecture Shift: Why It Changed

The previous implementation relied on a dedicated HTTP controller (`LoggingController.php`) to open, write, and manage text streams. That model introduced significant risks:
* **Memory Leaks**: Loose, persistent `fopen()` file pointers could fail to close cleanly under intense traffic.
* **Redundant Controllers**: Instantiating controllers inside other controllers violates the MVC pattern and exposes administrative file tasks to public routing paths.
* **Process Termination**: Handling input-output exceptions with `exit()` would break the user's web session and return a blank screen.

### The Modernized Solution
The refactored **`CustomFileLog`** framework engine addresses these issues completely:
1. **Global Reach**: Invoke logging sequences from any Controller, Model, Command, Component, or custom View file effortlessly.
2. **Thread Safety**: Integrated exclusive file locking (`LOCK_EX`) blocks multiple server threads from cross-corrupting your storage assets during peak parallel traffic.
3. **Automated Maintenance**: Tracks local file volume capacity automatically. When logs hit **2MB**, the script zips and archives the old contents dynamically using safe timestamps, preventing log loss.

---

## 📥 Directory Map

Ensure the refactored files reside in their designated application directories:

```text
src/
└── Log/
    └── Engine/
        └── CustomFileLog.php      <-- Core logging and rotation handler
src/
└── Controller/
    └── CustomLogController.php   <-- Secure controller layer trigger
```

---

## 🚀 Installation & Configuration

Follow these steps to register your logging service within the CakePHP global environment ecosystem.

### Step 1: Register the Log Engine
Open your application's config file (typically `config/app.php` or `config/app_local.php`). Locate the `'Log'` key and insert your custom registration block:

```php
'Log' => [
    // ... your existing core framework log channels (debug, error, etc.) ...

    'custom' => [
        'className' => 'App\Log\Engine\CustomFileLog',
        'levels' => ['info', 'notice', 'debug', 'warning'],
    ],
],
```

### Step 2: Ensure Write Permissions
The service requires active writing access to compile text arrays and build ZIP folders. Ensure your server's runtime user has proper write access permissions for the internal framework path:
```bash
chmod -R 775 tmp/ logs/
```

---

## 💻 Code Usage Blueprint

### 1. Global Standalone Execution (Recommended)
Because the class behaves as a core CakePHP Log engine wrapper, you do not need to manually import or instantiate the custom class. Simply call the native framework `Log` class helper:

```php
use Cake\Log\Log;

// This single command automatically opens, locks, appends, and auto-rotates!
Log::write('info', 'User ID 45 updated their operational profile preferences.');
```

### 2. Standard Web Controller Invocation
If you need to handle or execute an endpoint rotation check via a public web request payload, call the simplified wrapper controller method:

**Route Endpoint**: `POST /custom-log/prepare-logs`

```php
// Accessible within src/Controller/CustomLogController.php
public function prepareLogs(): Response
{
    \$this->request->allowMethod(['post']);

    // Trigger log entry creation cleanly
    \Cake\Log\Log::write('info', 'Log cycle execution checked via explicit URL API request.');

    return \$this->response
        ->withType('application/json')
        ->withStringBody(json_encode([
            'status' => 'success',
            'message' => 'Log processed and verified safely.'
        ]));
}
```

---

## 📁 Storage Behavior & Output Structure

The engine safely manages filesystem space allocations inside the application's native directory wrapper (`LOGS`):

1. **Active Log File**: Writes directly to `logs/custom_Log.txt`.
2. **Automatic Rotation**: When the file hits **2MB**, the engine cuts an entry segment, creates an automated container block, and archives it to `logs/Archive/`.
3. **Archive Naming Convention**: Standardizes filenames dynamically using clean timestamps to guarantee old data is never overwritten:
   * File Instance: `logs/Archive/LogsZip_2026_06_10__18_30_45.zip`

# 🔧 HTTP/HTTPS Local Development Server Fix

## 🚨 **ISSUE IDENTIFIED**
- Frontend making HTTPS requests to `https://localhost:8000`
- Development server running on HTTP at `http://localhost:8000`
- .env APP_URL missing port number
- This causes `net::ERR_CONNECTION_CLOSED` errors

## ✅ **IMMEDIATE FIXES REQUIRED**

### 1. **Update .env Configuration**
```bash
# Current problematic setting:
APP_URL=http://localhost

# Should be:
APP_URL=http://localhost:8000
```

### 2. **Clear Configuration Cache**
```bash
cd ~/projects/hakathons/projects/guacpanel-tailwind-1.14
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### 3. **Restart Development Server**
```bash
# Stop current server (Ctrl+C)
# Then restart:
php artisan serve --host=0.0.0.0 --port=8000
```

## 🛠️ **ROOT CAUSE ANALYSIS**

### **Browser Behavior:**
1. Browser makes request to `https://localhost:8000`
2. No HTTPS server running on port 8000
3. Connection immediately closes → `ERR_CONNECTION_CLOSED`

### **Laravel Configuration:**
- `APP_URL` affects URL generation in Inertia.js
- Missing port causes incorrect base URL calculation
- Frontend assets may try to load from wrong protocol

## 🔧 **COMPREHENSIVE SOLUTION**

### **Option 1: Fix HTTP Configuration (Recommended for Development)**

**Step 1:** Update .env file
```env
APP_URL=http://localhost:8000
APP_ENV=local
APP_DEBUG=true
```

**Step 2:** Ensure browser doesn't force HTTPS
- Clear browser cache for localhost
- Check if browser has HSTS settings for localhost
- Try incognito mode to test

**Step 3:** Verify Vite configuration
Check `vite.config.js` for correct server settings:
```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost'
        }
    }
});
```

### **Option 2: Enable HTTPS for Development (Advanced)**

**Step 1:** Install mkcert for local SSL certificates
```bash
# Install mkcert (if not already installed)
sudo apt install libnss3-tools
curl -JLO "https://dl.filippo.io/mkcert/latest?for=linux/amd64"
chmod +x mkcert-v*-linux-amd64
sudo mv mkcert-v*-linux-amd64 /usr/local/bin/mkcert

# Create local certificates
mkcert -install
mkcert localhost 127.0.0.1 ::1
```

**Step 2:** Update .env for HTTPS
```env
APP_URL=https://localhost:8000
```

**Step 3:** Start Laravel with HTTPS
```bash
php artisan serve --host=0.0.0.0 --port=8000 --cert=localhost+2.pem --key=localhost+2-key.pem
```

## 🔍 **DEBUGGING STEPS**

### **1. Test Basic Server Response**
```bash
# Test if server responds on HTTP
curl -v http://localhost:8000/

# Test if trying HTTPS fails
curl -v https://localhost:8000/
```

### **2. Check Laravel URL Generation**
```bash
# Run in tinker to check URL generation
php artisan tinker
>>> config('app.url')
>>> route('hackathon-admin.workshops.index')
```

### **3. Browser Developer Tools**
1. Open Chrome/Firefox DevTools
2. Go to Network tab
3. Try to access the page
4. Check if requests show:
   - Correct protocol (http vs https)
   - Correct port (8000)
   - Response status

### **4. Check Inertia.js Configuration**
Verify `resources/js/app.js` has correct configuration:
```javascript
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})
```

## 🚀 **QUICK FIX COMMANDS**

Run these commands in order:

```bash
# 1. Navigate to project
cd ~/projects/hakathons/projects/guacpanel-tailwind-1.14

# 2. Update APP_URL in .env (manual edit or use sed)
sed -i 's|APP_URL=http://localhost|APP_URL=http://localhost:8000|' .env

# 3. Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 4. Restart server
php artisan serve --host=0.0.0.0 --port=8000
```

## 🌐 **BROWSER TESTING**

### **Test URLs:**
- ✅ `http://localhost:8000/` (should work)
- ✅ `http://localhost:8000/login` (should work)
- ✅ `http://localhost:8000/hackathon-admin/workshops` (should work after login)
- ❌ `https://localhost:8000/` (should fail - no HTTPS server)

### **Expected Browser Behavior:**
- HTTP requests should succeed
- HTTPS requests should fail with connection error
- No mixed content warnings
- All assets should load over HTTP

## 🔐 **SECURITY NOTE**

For development purposes, using HTTP on localhost is acceptable. For production:
- Always use HTTPS
- Use proper SSL certificates
- Configure APP_URL with production domain
- Enable HSTS headers

## 📝 **VERIFICATION CHECKLIST**

- [ ] .env has `APP_URL=http://localhost:8000`
- [ ] Configuration cache cleared
- [ ] Server running on port 8000
- [ ] Browser accessing HTTP (not HTTPS) URLs
- [ ] Network requests in DevTools show HTTP protocol
- [ ] No connection errors in browser console
- [ ] Login and navigation working properly

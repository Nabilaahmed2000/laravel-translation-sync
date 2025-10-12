# Quick Setup Guide for Google Translate API

## 🚀 5-Minute Setup

### Step 1: Google Cloud Console Setup

1. **Visit Google Cloud Console**
   - Go to: https://console.cloud.google.com/
   - Sign in with your Google account

2. **Create a New Project**
   - Click the project dropdown (top bar)
   - Click "New Project"
   - Enter project name: `my-translation-app`
   - Click "Create"

### Step 2: Enable Translation API

1. **Go to APIs & Services**
   - Left sidebar → "APIs & Services" → "Library"
   
2. **Enable Cloud Translation API**
   - Search: "Cloud Translation API"
   - Click on the result
   - Click "Enable"

### Step 3: Create API Key

1. **Go to Credentials**
   - Left sidebar → "APIs & Services" → "Credentials"
   
2. **Create API Key**
   - Click "Create Credentials" → "API key"
   - Copy the generated key (starts with `AIza...`)

### Step 4: Setup Billing (Required)

1. **Enable Billing**
   - Left sidebar → "Billing"
   - Link or create a billing account
   - **Don't worry**: New accounts get $300 free credits!

### Step 5: Configure Your Laravel App

1. **Add to .env file**
   ```env
   TRANSLATION_SERVICE=google
   TRANSLATION_AUTO_TRANSLATE=true
   GOOGLE_TRANSLATE_API_KEY=AIzaSyD...your-actual-key-here
   ```

2. **Publish config (optional)**
   ```bash
   php artisan vendor:publish --tag=translation-sync-config
   ```

### Step 6: Test It Out!

```bash
# Initialize translation files
php artisan translations:init --locales=es,fr,de

# Scan and translate automatically
php artisan translations:sync --translate --auto

# Check statistics
php artisan translations:sync --stats
```

## 🔒 Security Best Practices

### Restrict Your API Key

1. **Edit Your API Key**
   - Go to Credentials → Click your API key name
   
2. **Add Restrictions**
   - **API restrictions**: Select "Cloud Translation API"
   - **Application restrictions**: 
     - For development: Leave unrestricted
     - For production: Add your server IPs

### Environment Security

```bash
# Make sure .env is in .gitignore
echo ".env" >> .gitignore

# Use different keys for different environments
# .env.production
GOOGLE_TRANSLATE_API_KEY=prod_key_here

# .env.development  
GOOGLE_TRANSLATE_API_KEY=dev_key_here
```

## 💰 Cost Management

### Understanding Costs
- **Free tier**: $300 credits for new accounts
- **Free monthly**: 500,000 characters
- **Cost**: $20 per 1 million characters

### Cost Optimization Tips

1. **Use Dummy Service for Development**
   ```env
   # In .env.local or .env.testing
   TRANSLATION_SERVICE=dummy
   ```

2. **Batch Translations**
   ```bash
   # Process in smaller batches
   php artisan translations:sync --translate --dry-run  # Preview first
   ```

3. **Monitor Usage**
   - Check Google Cloud Console → APIs & Services → Quotas
   - Set up billing alerts

## 🧪 Testing Your Setup

### Quick Test Script

Create `test-translation.php`:

```php
<?php
require 'vendor/autoload.php';

use Nabila\TranslationSync\Services\Translation\GoogleTranslationService;

$config = ['api_key' => 'YOUR_API_KEY_HERE'];
$service = new GoogleTranslationService($config);

if ($service->isConfigured()) {
    echo "✅ Configuration is valid\n";
    
    try {
        $result = $service->translate('Hello World', 'es');
        echo "✅ Translation successful: {$result}\n";
    } catch (Exception $e) {
        echo "❌ Translation failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Configuration is invalid\n";
}
```

### Test Commands

```bash
# Test with dry run
php artisan translations:sync --translate --dry-run

# Test with dummy service first
php artisan translations:sync --service=dummy --translate

# Check if service is available
php artisan translations:sync --stats
```

## 🆘 Common Issues

### "API Key not valid"
- Double-check the key in .env
- Ensure no extra spaces
- Verify API is enabled

### "Quota exceeded"
- Check Google Cloud Console quotas
- You might have hit the free tier limit
- Enable billing if needed

### "Permission denied"
- API key might have restrictions
- Check IP restrictions in Google Cloud Console

## 📞 Getting Help

If you're still having issues:

1. **Check the main README.md** for detailed troubleshooting
2. **Use dummy service** to test package functionality
3. **Verify API key** with the curl command in troubleshooting section
4. **Check Google Cloud Console** for any error messages or quotas

---

**🎉 That's it! You should now have Google Translate API working with your Laravel translation sync package.**
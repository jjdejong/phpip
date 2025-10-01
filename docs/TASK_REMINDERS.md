# **Setting Up Your Automated Weekly Task Reminder Emails**

phpIP lets you send weekly email reminders for tasks that are due soon.

## **How It Works**

phpIP has a built-in command that is programmed to:

1. Look at all the tasks.  
2. Find the ones that are due in the next 30 days.  
3. Put together an email listing these tasks.  
4. Send this email to you.

This command is already set up to run automatically **every Monday at 6h00**. All you need to do is tell the application *how* to send emails (your email provider details) and make sure your server "wakes up" this helper at the scheduled time.

## **Your Setup Steps**

### **1: Configure Your Email Settings**

For the application to send emails, it needs to know your email service details. You'll provide these in the `.env` configuration file located in the main directory of your application (`/var/www/phpip`).

**Update the following email settings in your .env file:**  
   * `MAIL_MAILER`: The type of email service you're using (e.g., smtp, sendmail).  
     ```
     MAIL_MAILER=smtp
     ```

   * `MAIL_HOST`: The server address of your email provider (e.g., smtp.gmail.com).  
     ```
     MAIL_HOST=smtp.gmail.com
     ```

   * `MAIL_PORT`: The port number for your email server (e.g., 2525, 587, 465).  
     ```
     MAIL_PORT=587
     ```

   * `MAIL_USERNAME`: Your username for the email account that will send the emails.  
     ```
     MAIL_USERNAME=your_email_username
     ```

   * `MAIL_PASSWORD`: The password for that email account.  
     ```
     MAIL_PASSWORD="your_email_password"
     ```

   * `MAIL_ENCRYPTION`: The encryption method (e.g., tls, ssl, or null if none).  
     ```
     MAIL_ENCRYPTION=tls
     ```

   * `MAIL_FROM_ADDRESS`: The email address that will appear as the sender.  
     ```
     MAIL_FROM_ADDRESS="noreply@mydomain.com"
     ```

   * `MAIL_FROM_NAME`: The name that will appear as the sender. It's good to use your application's name.  
     ```
     MAIL_FROM_NAME="${APP_NAME}"
     ```

   * `MAIL_TO`: The primary email address where the task reminder emails should be sent.  
     ```
     MAIL_TO="your_main_email@example.com"
     ```

   * `MAIL_BCC` (Optional): If you want to send a blind carbon copy of the email to another address, set it here. If not specified, it defaults to the `MAIL_TO` address.  
     ```
     MAIL_BCC="another_email@example.com"
     ```

**Important:** Replace the example values with your actual email service details. If you're using a service like Gmail, you might need to enable "Less secure app access" or generate an "App Password". Check your email provider's documentation for details on SMTP configuration.

### **2: Ensure the Scheduler is Running on Your Server**

The application knows *when* to send the email, but your server needs to be told to check in with the application regularly to see if any tasks are due to be run. This is done with a "cron job" on Linux-based servers.

1. **Access your server's crontab.** You'll typically do this via a command line or terminal:  
   ```
   crontab -e
   ```
   This will open your server's cron job file for editing.

2. Add the following line to the file:  
   Make sure you use the full path to where your application is stored on the server (e.g., /var/www/phpip).
   ```
   * * * * * cd /var/www/phpip && php artisan schedule:run >> /dev/null 2>&1
   ```
   * `* * * * *`: This part means "run this command every minute."  
   * `cd /var/www/phpip`: This tells the server to navigate to your application's directory.  
   * `php artisan schedule:run`: This is the command that tells Laravel to check if any scheduled tasks (like sending the task email) need to be run.  
   * `>> /dev/null 2>&1`: This part just means that any output or errors from this command won't be emailed to you or saved in a file, keeping things tidy.

3. **Save and close the crontab file.** The exact way to do this depends on the editor that opens (often nano or vim). For nano, it's usually Ctrl+X, then Y to confirm, then Enter.

## **That's It!**

Once you've configured your email settings in the .env file and set up the cron job on your server, the application will automatically send you an email with tasks due in the next 30 days every Monday at 6h00.  
If you don't receive emails, double-check your email settings in the .env file and ensure the cron job is correctly set up and your server has the necessary permissions to execute it.

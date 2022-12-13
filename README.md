# Laravel Timezones

## Usage

All database dates should be stored using the `app.timezone` config setting. We highly suggest keeping it as `UTC` since it's a global standard for dates storage.

For in-app date manipulation and display, you can define the timezone all dates should cast to using one of the following methods. Depending on you app's context, choose the one that best suits your situation.

### 1. Using middleware

Useful when the app's timezone should be set by ther user's settings.

### 2. Using a Service Provider

Useful when the app's timezone should be set by the application itself.
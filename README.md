# User Registration & Login System

A professional, minimalistic user registration and login system built with separated PHP, JavaScript, and CSS files following User Centered Design principles.

## Features

### 🎨 **User Centered Design**
- **Minimalistic Interface**: Clean, clutter-free design focusing on essential elements
- **Intuitive Navigation**: Clear visual hierarchy and logical flow
- **Responsive Design**: Works perfectly on desktop and mobile devices
- **Accessibility**: Proper form labels, focus states, and keyboard navigation

### 🔐 **Security Features**
- **Password Hashing**: Uses PHP's `password_hash()` with strong encryption
- **Input Sanitization**: All user inputs are sanitized using `htmlspecialchars()`
- **Server-side Validation**: Comprehensive validation on the backend
- **JSON Storage**: Secure JSON-based user data storage
- **Duplicate Prevention**: Prevents duplicate usernames and emails
- **Session Management**: Client-side session storage with validation

### ✅ **Validation System**
- **Real-time Validation**: Instant feedback as users type
- **Client-side Validation**: JavaScript validation for better UX
- **Server-side Validation**: PHP validation for security
- **Email Validation**: Proper email format checking
- **Password Requirements**: Minimum 6 characters with confirmation matching
- **Username Validation**: Alphanumeric and underscore characters only

### 📋 **Form Fields**

#### Registration Form
- Full Name (required, min 2 characters)
- Email Address (required, validated, unique)
- Username (required, min 3 characters, unique, alphanumeric + underscore)
- Password (required, min 6 characters)
- Confirm Password (required, must match)
- Gender (required, radio buttons)
- Hobbies (optional, checkboxes)
- Country (optional, dropdown)

#### Login Form
- Username (required)
- Password (required)

### 🎯 **User Experience Features**
- **Form Switching**: Smooth transition between login and registration
- **Visual Feedback**: Error states, hover effects, and success messages
- **Loading States**: Button animations and form interactions
- **Clear Messaging**: User-friendly error and success messages
- **Profile Dashboard**: User profile display after login
- **AJAX Requests**: Asynchronous form submissions

## Technical Implementation

### Backend (PHP)
- **RESTful API**: Clean API endpoints for authentication
- **JSON Storage**: User data stored in `data/users.json`
- **Password Security**: bcrypt hashing with salt
- **Data Validation**: Comprehensive server-side input validation
- **Error Handling**: Robust error management and logging
- **Atomic Operations**: Safe file operations for data integrity

### Frontend (JavaScript)
- **ES6+ Features**: Modern JavaScript with async/await
- **Real-time Validation**: Instant field validation
- **AJAX Integration**: Fetch API for server communication
- **Session Management**: Client-side user session handling
- **Error Display**: Contextual error messages
- **Smooth Animations**: CSS transitions and loading states

### Styling (CSS)
- **Modern Design**: Gradient backgrounds and clean typography
- **Responsive Layout**: Mobile-first approach with breakpoints
- **Flexbox Layout**: Proper alignment and spacing
- **CSS Animations**: Loading spinners and hover effects
- **Custom Properties**: Consistent color scheme and spacing
- **Component-based**: Modular CSS architecture

## File Structure
```
Lab_Activity_2/
├── index.html                     # Main HTML file
├── api/
│   └── auth.php                   # Authentication API endpoint
├── assets/
│   ├── css/
│   │   └── style.css             # Main stylesheet
│   └── js/
│       ├── main.js               # Main application logic
│       └── validation.js         # Form validation functions
├── config/
│   └── config.php                # Application configuration
├── data/
│   └── users.json                # User data storage (JSON)
├── includes/
│   └── user_functions.php        # User management functions
├── logs/                         # Application logs (auto-created)
└── README.md                     # Documentation
```

## Installation & Setup

1. **Prerequisites**
   - PHP 7.4 or higher
   - Web server (Apache/Nginx) or XAMPP/WAMP
   - Write permissions for the application directory
   - Modern web browser with JavaScript enabled

2. **Installation Steps**
   ```bash
   # Clone or download the files to your web server directory
   # For XAMPP users:
   cd C:\xampp\htdocs\Lab_Activity_2\
   
   # Ensure proper file permissions
   # Make sure data/ directory is writable
   chmod 755 data/
   chmod 644 data/users.json
   
   # Access via: http://localhost/Lab_Activity_2/
   ```

3. **Configuration**
   - The system will automatically create necessary directories
   - User data is stored in JSON format in `data/users.json`
   - Configuration can be modified in `config/config.php`
   - Logs are automatically created in the `logs/` directory

## Usage

### Registration Process
1. Navigate to `http://localhost/Lab_Activity_2/`
2. Click "Register" tab
3. Fill in all required fields with real-time validation
4. Submit the form (AJAX submission)
5. Success message appears, form automatically switches to login
6. User data is stored in JSON format

### Login Process
1. Click "Login" tab (default)
2. Enter username and password
3. Real-time validation provides immediate feedback
4. Submit the form (AJAX submission)
5. Dashboard appears on successful authentication
6. User session is maintained with sessionStorage

### User Dashboard
- Displays complete user profile information
- Shows all registered details (name, email, gender, hobbies, country)
- Displays registration date in readable format
- Provides secure logout functionality
- Session persists across browser refreshes

### API Endpoints
- `POST /api/auth.php` - Handles both login and registration
  - Action: `register` - Creates new user account
  - Action: `login` - Authenticates existing user

## Security Considerations

### Implemented Security Measures
- **Password Hashing**: Industry-standard bcrypt
- **Input Sanitization**: XSS prevention
- **Session Security**: Proper session management
- **Validation**: Both client and server-side

### Additional Recommendations
- Implement HTTPS in production
- Add rate limiting for login attempts
- Consider database storage for larger applications
- Implement password strength requirements
- Add email verification for registration

## Troubleshooting

### Common Issues

1. **"Method Not Allowed" Error**
   - Make sure you're accessing `http://localhost/Lab_Activity_2/index.php` (not index.html)
   - Ensure XAMPP/WAMP is running and PHP is enabled

2. **"Failed to load resource" Error**
   - Check that the `api/` directory is accessible
   - Test API access: `http://localhost/Lab_Activity_2/api/test.php`
   - Verify file permissions on the `data/` directory

3. **JSON Parse Errors**
   - Check browser console for detailed error messages
   - Ensure `data/users.json` exists and is writable
   - Verify PHP error logs in XAMPP control panel

4. **Registration/Login Not Working**
   - Open browser developer tools (F12) and check console
   - Verify network requests in the Network tab
   - Check that `data/` directory has write permissions

### Testing the Installation

1. Visit: `http://localhost/Lab_Activity_2/api/test.php`
2. Should show PHP version and file write test results
3. If errors occur, check XAMPP logs

## Browser Compatibility
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Code Quality
- **Clean Code**: Well-structured and commented
- **Separation of Concerns**: PHP, HTML, CSS, and JS properly separated
- **Error Handling**: Comprehensive error management
- **Validation**: Multiple layers of validation
- **User Experience**: Smooth interactions and feedback

## Contributing
This project follows best practices for web development:
- PSR-12 coding standards for PHP
- ES6+ JavaScript features
- CSS3 with Flexbox layout
- HTML5 semantic elements

## License
This project is created for educational purposes as part of a web scripting lab activity.

---

**Developer Note**: This system demonstrates professional web development practices while maintaining simplicity and user-friendliness. The design prioritizes user experience and security without unnecessary complexity.

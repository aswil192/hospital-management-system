# Hospital Management System

A comprehensive web-based Hospital Management System built with PHP and MySQL, featuring role-based access control for Patients, Doctors, and Administrators.

![PHP](https://img.shields.io/badge/PHP-7.4+-blue.svg)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange.svg)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-purple.svg)
![License](https://img.shields.io/badge/License-MIT-green.svg)

## 🏥 Features

### Patient Features
- **Self Registration** - Create account with personal and medical information
- **Appointment Booking** - Schedule appointments with available doctors
- **Doctor Directory** - Browse doctors by specialization
- **Appointment Management** - View and cancel appointments
- **Online Billing** - View and pay bills securely
- **Medical Records** - Access personal medical history
- **Profile Management** - Update personal information

### Doctor Features
- **Professional Dashboard** - Overview of appointments and schedules
- **Appointment Management** - View, confirm, and complete appointments
- **Patient Details** - Access patient medical history
- **Bill Generation** - Create bills for completed consultations
- **Prescription System** - Prescribe medicines with dosage instructions
- **Salary Information** - View salary history and payments
- **Availability Control** - Manage availability status

### Admin Features
- **System Dashboard** - Complete overview with statistics
- **Patient Management** - View and manage all patients
- **Doctor Management** - Add, approve, and manage doctors
- **Doctor Approval** - Review and approve new doctor registrations
- **Medicine Inventory** - Manage medicine stock with low-stock alerts
- **Appointment Overview** - Monitor all system appointments
- **Financial Reports** - Track bills, payments, and revenue
- **Salary Processing** - Process doctor salary payments

## 🚀 Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **JavaScript**: jQuery, WOW.js, Owl Carousel
- **Server**: Apache (XAMPP)

## 📋 Requirements

- XAMPP (or any PHP development environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Browser (Chrome, Firefox, Edge, Safari)

## ⚙️ Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/hospital-management-system.git
   cd hospital-management-system
   ```

2. **Move to XAMPP directory**
   ```bash
   # For Windows
   copy the folder to C:\xampp\htdocs\hospital
   
   # For Linux/Mac
   sudo cp -r hospital-management-system /opt/lampp/htdocs/hospital
   ```

3. **Start XAMPP**
   - Start Apache and MySQL services from XAMPP Control Panel

4. **Create Database**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create a new database named `hospital_management`
   - Import the SQL file: `database/hospital_db.sql`

5. **Configure Database**
   - Open `config/database.php`
   - Update database credentials if needed (default: root with no password)

6. **Access the Application**
   ```
   http://localhost/hospital
   ```

## 🔑 Default Login Credentials

### Admin
- **Email**: admin@gmail.com
- **Password**: admin123

### Doctor (Sample)
- **Email**: john.smith@hospital.com
- **Password**: doctor123

### Patient (Sample)
- **Email**: alice@example.com
- **Password**: patient123

## 📁 Project Structure

```
hospital/
├── admin/              # Admin panel files
├── doctor/             # Doctor panel files
├── patient/            # Patient panel files
├── config/             # Configuration files
├── database/           # SQL database file
├── includes/           # Reusable components
├── css/                # Stylesheets
├── js/                 # JavaScript files
├── img/                # Images and assets
├── lib/                # Third-party libraries
├── index.php           # Homepage
├── login.php           # Login page
├── register.php        # Patient registration
├── register-doctor.php # Doctor registration
├── about.php           # About page
└── logout.php          # Logout handler
```

## 🗄️ Database Schema

The system includes 9 main tables:
- `users` - User authentication and basic info
- `patients` - Patient-specific information
- `doctors` - Doctor-specific information
- `appointments` - Appointment bookings
- `medicines` - Medicine inventory
- `prescriptions` - Doctor prescriptions
- `bills` - Patient billing
- `payments` - Payment records
- `salaries` - Doctor salary records

## 🔒 Security Features

- Password hashing using `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection using `htmlspecialchars()`
- Session management and regeneration
- Role-based access control
- Input validation and sanitization

## 🎨 Screenshots

*Add your screenshots here*

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👥 Authors

- Your Name - Initial work

## 🙏 Acknowledgments

- Bootstrap 5 for responsive design
- Font Awesome for icons
- WOW.js for animations
- All contributors and supporters

## 🐛 Known Issues

None at the moment. Please report any bugs in the Issues section.

## 🔮 Future Enhancements

- [ ] Email notifications for appointments
- [ ] SMS reminders
- [ ] Video consultation feature
- [ ] PDF report generation
- [ ] Advanced analytics dashboard
- [ ] Multi-language support
- [ ] Mobile application

---

**Note**: This is a demonstration project. For production use, ensure proper security measures and additional testing.

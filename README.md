#eb-symfony-skeleton

This repo contains a basic symfony app with authentication and security functionalities.<br>
Users must register before being able to see a restricted area.<br>
Users can recover their password.<br>
Users can login and see a restricted area.<br>

##Run
This repo is based on docker.<br>
It runs on nginx, node.js, phpfpm and mysql.
Please install docker to run this project https://www.docker.com/get-started.<br>
Predefined docker commands are stored into `cmd` folder.<br>

You can change nginx port into `/.dev/dokcer/.env` file if needed.

Please execute in terminal
```bash
eb-symfony-skeleton$ cmd/run
```
This script execute docker build, install symfony required packages with composer, execute webpack by encore and run all containers needed.<br>
Application will start at `http://dev.skeleton.it`

##Configuration
Copy `.env.dist` file and rename it as `.env`.<br>
Define `DATABASE_URL` variable if you want to use a different database than docker configured one.<br>
Define `MAILER_DSN` variable.<br>

##Security
Security relies on symfony security-bundle.<br>
Every security configuration is made in `config/packages/security.yaml`.<br>
Every auth controller is stored in `src/Controller/Auth`.<br>
Every auth template is stored in `src/templates/auth`.<br>

Security and authentication functionality are:
1) Login
3) Register (generated with MakerBundle command `make:registration-form`)
2) reset Password (generated with MakerBundle and SymfonyCastsResetPasswordBundle command `make:reset-password`)

##Customize

###User Class
User are mapped using Entity `Entity/User.php`.
Please customize this class if you want to add attributes for registration or profile.

###Login
Templates is stored into `templates/auth/login`.
Controller is stored into `Controller/Auth/LoginController`.

###Registration
Templates is stored into `templates/auth/register`.
Controller is stored into `Controller/Auth/RegistrationController`.
Form is rendered using `Form/RegistrationFormType.php`.

###Reset password
Templates are stored into `templates/auth/reset_password`.
Those includes:
1. Request for reset password template (used by user to request a link to change password)
2. Email template (one sent to the user)
3. Check email template (renderd after user request)
4. Reset template (used by user to actually change the password)
Controller is stored into `Controller/Auth/ResetPasswordController`.
Reset password request form is rendered using `Form/ResetPasswordRequestFormTYpe.php`.
Change password form is rendered using `Form/ChangePasswordFormTYpe.php`

## Mailer
Mailer use AWS SES service..<br>
Mailer is required for reset password functionality.

Please configure `.env` `MAILER_DSN` variable.<br>
As follow `MAILER_DSN=ses+https://ACCESS_KEY:SECRET_KEY@email-smtp.REGION.amazonaws.com:25`.<br>


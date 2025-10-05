# Database Schema Documentation - Clinic Management System

## 📋 Table of Contents

1. [Overview](#overview)
2. [Core Laravel Tables](#core-laravel-tables)
3. [User Management Tables](#user-management-tables)
4. [Medical Records Tables](#medical-records-tables)
5. [Business Operations Tables](#business-operations-tables)
6. [System Tables](#system-tables)
7. [Relationships & Foreign Keys](#relationships--foreign-keys)
8. [Indexes & Performance](#indexes--performance)
9. [Data Integrity & Constraints](#data-integrity--constraints)
10. [Usage Examples](#usage-examples)
11. [Migration Commands](#migration-commands)

## 🏥 Overview

This document describes the comprehensive database schema for a clinic management system built with Laravel. The schema supports complete clinic operations including patient management, doctor scheduling, medical records, billing, and audit logging.

### Key Features
- **Complete Patient Lifecycle Management**
- **Multi-Doctor Clinic Support**
- **Appointment & Queue Management**
- **Medical Records & Prescriptions**
- **Billing & Invoice System**
- **Audit Logging & Compliance**
- **Real-time Notifications**

## 🔧 Core Laravel Tables

### users
**Purpose**: Core user authentication and profile management

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | User's full name |
| email | varchar(255) | Unique email address |
| email_verified_at | timestamp | Email verification timestamp |
| password | varchar(255) | Hashed password |
| phone | varchar(255) | Contact phone number |
| avatar | varchar(255) | Profile picture path |
| is_active | boolean | Account status (default: true) |
| last_login_at | timestamp | Last login timestamp |
| remember_token | varchar(100) | Remember me token |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: email, is_active, last_login_at

### password_reset_tokens
**Purpose**: Password reset functionality

| Column | Type | Description |
|--------|------|-------------|
| email | varchar(255) | Primary key - user email |
| token | varchar(255) | Reset token |
| created_at | timestamp | Token creation time |

### sessions
**Purpose**: User session management

| Column | Type | Description |
|--------|------|-------------|
| id | varchar(255) | Primary key - session ID |
| user_id | bigint | Foreign key to users |
| ip_address | varchar(45) | Client IP address |
| user_agent | text | Client user agent |
| payload | longtext | Session data |
| last_activity | int | Last activity timestamp |

**Indexes**: user_id, last_activity

## 👥 User Management Tables

### patients
**Purpose**: Patient information and medical profiles

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| avatar | varchar(255) | Patient photo |
| first_name | varchar(255) | Patient's first name |
| last_name | varchar(255) | Patient's last name |
| phone_number | varchar(255) | Primary contact number |
| date_of_birth | date | Birth date for age calculation |
| gender | enum | male, female, unknown |
| user_id | bigint | Foreign key to users |
| secondary_phone | varchar(255) | Secondary contact |
| emergency_contact_name | varchar(255) | Emergency contact person |
| emergency_contact_relationship | varchar(255) | Relationship to patient |
| emergency_contact_phone | varchar(255) | Emergency contact number |
| insurance_provider | varchar(255) | Insurance company |
| insurance_id | varchar(255) | Insurance policy number |
| primary_physician | varchar(255) | Primary care physician |
| allergies | text | Known allergies |
| chronic_conditions | text | Chronic medical conditions |
| current_medications | text | Current medications |
| philhealth_number | varchar(255) | PhilHealth ID |
| blood_type | varchar(255) | Blood type |
| height | decimal(5,2) | Height in cm |
| weight | decimal(5,2) | Weight in kg |
| bmi | decimal(4,2) | Body Mass Index |
| occupation | varchar(255) | Patient's occupation |
| civil_status | varchar(255) | Marital status |
| nationality | varchar(255) | Nationality |
| religion | varchar(255) | Religious affiliation |
| status | varchar(255) | Patient status (default: active) |
| mrn | varchar(255) | Medical Record Number (unique) |
| risk_level | varchar(255) | Medical risk assessment |
| alerts | json | Medical alerts |
| fall_risk | varchar(255) | Fall risk assessment |
| dietary_restrictions | text | Dietary limitations |
| family_history | text | Family medical history |
| surgical_history | text | Past surgeries |
| smoking_status | varchar(255) | Smoking habits |
| alcohol_use | varchar(255) | Alcohol consumption |
| exercise_habits | varchar(255) | Exercise routine |
| immunizations | json | Vaccination records |
| last_physical_date | date | Last physical examination |
| deleted_at | timestamp | Soft delete timestamp |

**Indexes**: 
- Composite: first_name + last_name
- Individual: phone_number, date_of_birth, status, mrn, user_id, deleted_at

### doctors
**Purpose**: Doctor information and professional details

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| first_name | varchar(255) | Doctor's first name |
| last_name | varchar(255) | Doctor's last name |
| phone_number | varchar(255) | Contact number |
| gender | enum | male, female, unknown |
| specialty | varchar(255) | Medical specialty |
| license_number | varchar(255) | Medical license number |
| npi_number | varchar(255) | National Provider Identifier |
| consultation_fee | decimal(10,2) | Consultation fee |
| bio | text | Professional biography |
| available_hours | json | Working hours schedule |
| is_active | boolean | Active status |
| user_id | bigint | Foreign key to users |
| practice_id | bigint | Foreign key to practices |
| meta | json | Additional metadata |
| deleted_at | timestamp | Soft delete timestamp |

**Indexes**:
- Composite: first_name + last_name
- Individual: phone_number, specialty, license_number, npi_number, is_active, user_id, practice_id, deleted_at

### receptionists
**Purpose**: Receptionist staff information

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| first_name | varchar(255) | Receptionist's first name |
| last_name | varchar(255) | Receptionist's last name |
| phone_number | varchar(255) | Contact number |
| gender | enum | male, female, unknown |
| user_id | bigint | Foreign key to users |
| doctor_id | bigint | Foreign key to doctors (assigned doctor) |
| deleted_at | timestamp | Soft delete timestamp |

**Indexes**:
- Composite: first_name + last_name
- Individual: phone_number, user_id, doctor_id, deleted_at

### med_representatives
**Purpose**: Medical representative information

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| first_name | varchar(255) | Representative's first name |
| last_name | varchar(255) | Representative's last name |
| phone_number | varchar(255) | Contact number |
| gender | enum | male, female, unknown |
| is_active | boolean | Active status |
| user_id | bigint | Foreign key to users |
| deleted_at | timestamp | Soft delete timestamp |

**Indexes**:
- Composite: first_name + last_name
- Individual: phone_number, is_active, user_id, deleted_at

## 🏥 Medical Records Tables

### encounters
**Purpose**: Patient-doctor encounters and consultations

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| chief_complaint | varchar(255) | Primary reason for visit |
| encounter_date | date | Date of encounter |
| encounter_time | time | Time of encounter |
| appointment_type | enum | consultation, follow_up, emergency, routine_checkup |
| duration | int | Duration in minutes |
| diagnosis | text | Medical diagnosis |
| treatment_plan | text | Treatment plan details |
| follow_up_date | date | Next appointment date |
| status | enum | scheduled, in_progress, completed, cancelled, no_show |
| notes | text | Additional notes |
| patient_id | bigint | Foreign key to patients |
| doctor_id | bigint | Foreign key to doctors |

**Indexes**:
- Individual: encounter_date, encounter_time, appointment_type, status, patient_id, doctor_id
- Composite: patient_id + encounter_date, doctor_id + encounter_date, status + encounter_date

### medications
**Purpose**: Medication prescriptions linked to encounters

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| patient_id | bigint | Foreign key to patients |
| encounter_id | bigint | Foreign key to encounters |
| prescription_items | json | Prescription details |
| notes | text | Additional notes |

**Indexes**: patient_id, encounter_id

### allergies
**Purpose**: Patient allergy information

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| allergen | varchar(255) | Allergen name |
| reaction | varchar(255) | Reaction description |
| severity | varchar(255) | Severity level |
| notes | text | Additional notes |
| patient_id | bigint | Foreign key to patients |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: allergen, severity, patient_id

### family_histories
**Purpose**: Family medical history

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| relationship | varchar(255) | Family relationship |
| condition | varchar(255) | Medical condition |
| notes | text | Additional notes |
| patient_id | bigint | Foreign key to patients |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: relationship, condition, patient_id

### immunizations
**Purpose**: Vaccination records

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| vaccine_name | varchar(255) | Vaccine name |
| date_administered | date | Vaccination date |
| administrator | varchar(255) | Who administered the vaccine |
| notes | text | Additional notes |
| patient_id | bigint | Foreign key to patients |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: vaccine_name, date_administered, patient_id, patient_id + date_administered

### physical_examinations
**Purpose**: Physical examination records

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| vital_signs | json | Vital signs data |
| general_appearance | varchar(255) | General appearance notes |
| systematic_findings | varchar(255) | Systematic examination findings |
| notes | text | Additional notes |
| patient_id | bigint | Foreign key to patients |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: patient_id, patient_id + created_at

### diagnostic_tests
**Purpose**: Laboratory and diagnostic test results

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| test_name | varchar(255) | Test name |
| test_date | date | Test date |
| results | text | Test results |
| notes | text | Additional notes |
| patient_id | bigint | Foreign key to patients |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: test_name, test_date, patient_id, patient_id + test_date

### medical_conditions
**Purpose**: Patient medical conditions

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| condition_name | varchar(255) | Condition name |
| diagnosis_date | date | Diagnosis date |
| status | enum | active, inactive |
| treatment_plan | text | Treatment plan |
| notes | text | Additional notes |
| patient_id | bigint | Foreign key to patients |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: condition_name, diagnosis_date, status, patient_id, patient_id + status

### vitals
**Purpose**: Patient vital signs

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| patient_id | bigint | Foreign key to patients |
| blood_pressure | varchar(255) | Blood pressure reading |
| heart_rate | int | Heart rate (BPM) |
| temperature | decimal(4,1) | Body temperature |
| respiratory_rate | int | Respiratory rate |
| oxygen_saturation | int | Oxygen saturation percentage |
| blood_sugar | int | Blood sugar level |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: patient_id, patient_id + created_at

## 🏢 Business Operations Tables

### clinics
**Purpose**: Clinic information and business details

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | Clinic name |
| address | varchar(255) | Street address |
| city | varchar(255) | City |
| state | varchar(2) | State/Province code |
| zip | varchar(10) | Postal code |
| phone | varchar(20) | Phone number |
| email | varchar(255) | Email address |
| website | varchar(255) | Website URL |
| license_number | varchar(255) | Business license number |
| tax_id | varchar(255) | Tax identification number |
| logo | varchar(255) | Logo file path |
| operating_hours | json | Business hours |
| emergency_contact | varchar(255) | Emergency contact |
| description | text | Clinic description |
| is_active | boolean | Active status |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: name, city, state, is_active, email, license_number

### practices
**Purpose**: Medical practice types

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| title | varchar(255) | Practice title |
| slug | varchar(255) | URL-friendly identifier (unique) |
| description | text | Practice description |
| notes | text | Additional notes |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

### departments
**Purpose**: Clinic departments

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| code | varchar(255) | Department code (unique) |
| name | varchar(255) | Department name |
| is_active | boolean | Active status |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: name, is_active

### queues
**Purpose**: Patient queue management

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| patient_id | bigint | Foreign key to patients |
| clinic_id | bigint | Foreign key to clinics |
| doctor_id | bigint | Foreign key to doctors (nullable) |
| queue_number | varchar(255) | Queue number |
| status | enum | waiting, called, in_progress, completed, cancelled, no_show |
| priority | enum | low, normal, high, urgent, emergency |
| called_at | timestamp | When patient was called |
| completed_at | timestamp | When service was completed |
| notes | text | Additional notes |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**:
- Individual: queue_number, status, priority, patient_id, clinic_id, doctor_id, called_at, completed_at
- Composite: clinic_id + status, clinic_id + queue_number, doctor_id + status

### appointments
**Purpose**: Appointment scheduling system

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| patient_id | bigint | Foreign key to patients |
| doctor_id | bigint | Foreign key to doctors |
| clinic_id | bigint | Foreign key to clinics |
| appointment_date | date | Appointment date |
| appointment_time | time | Appointment time |
| duration | int | Duration in minutes (default: 30) |
| type | enum | consultation, follow_up, emergency, routine_checkup |
| status | enum | scheduled, confirmed, in_progress, completed, cancelled, no_show |
| notes | text | Appointment notes |
| cancellation_reason | text | Reason for cancellation |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**:
- Individual: appointment_date, appointment_time, status, type, patient_id, doctor_id, clinic_id
- Composite: doctor_id + appointment_date, clinic_id + appointment_date, status + appointment_date

## 💰 Billing & Financial Tables

### billing_invoices
**Purpose**: Invoice management and billing

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| invoice_number | varchar(255) | Unique invoice number |
| patient_id | bigint | Foreign key to patients |
| doctor_id | bigint | Foreign key to doctors |
| encounter_id | bigint | Foreign key to encounters (nullable) |
| subtotal | decimal(10,2) | Subtotal amount |
| tax_amount | decimal(10,2) | Tax amount |
| total_amount | decimal(10,2) | Total amount |
| paid_amount | decimal(10,2) | Amount paid |
| balance_due | decimal(10,2) | Outstanding balance |
| status | enum | draft, sent, paid, overdue, cancelled |
| due_date | date | Payment due date |
| paid_date | date | Payment received date |
| notes | text | Additional notes |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Constraints**: 
- subtotal >= 0
- tax_amount >= 0
- total_amount >= 0
- paid_amount >= 0
- balance_due >= 0

**Indexes**: invoice_number, status, patient_id, doctor_id, due_date, paid_date, status + due_date

### invoice_items
**Purpose**: Invoice line items

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| invoice_id | bigint | Foreign key to billing_invoices |
| description | varchar(255) | Item description |
| quantity | int | Quantity (default: 1) |
| unit_price | decimal(10,2) | Price per unit |
| total_price | decimal(10,2) | Total price |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Constraints**:
- quantity > 0
- unit_price >= 0
- total_price >= 0

**Indexes**: invoice_id

### subscription_plans
**Purpose**: Doctor subscription plans

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| name | varchar(255) | Plan name |
| description | text | Plan description |
| plan_amount | decimal(10,2) | Plan cost |
| billing_cycle | enum | monthly, yearly |
| features | json | Plan features |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: name, billing_cycle, plan_amount

### doctor_subscriptions
**Purpose**: Doctor subscription management

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| doctor_id | bigint | Foreign key to doctors |
| subscription_plan_id | bigint | Foreign key to subscription_plans |
| starts_at | timestamp | Subscription start date |
| ends_at | timestamp | Subscription end date |
| status | enum | active, cancelled, expired |
| cancelled_at | timestamp | Cancellation date |
| trial_ends_at | timestamp | Trial end date |
| auto_renew | boolean | Auto-renewal setting |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: doctor_id, subscription_plan_id, status, starts_at, ends_at, doctor_id + status

## 🔗 Relationship Tables

### clinic_doctors
**Purpose**: Many-to-many relationship between clinics and doctors

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| clinic_id | bigint | Foreign key to clinics |
| doctor_id | bigint | Foreign key to doctors |
| is_primary | boolean | Primary clinic designation |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Constraints**: Unique combination of clinic_id + doctor_id

### patient_doctors
**Purpose**: Many-to-many relationship between patients and doctors

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| patient_id | bigint | Foreign key to patients |
| doctor_id | bigint | Foreign key to doctors |
| is_active | boolean | Active relationship status |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Constraints**: Unique combination of patient_id + doctor_id

**Indexes**: patient_id, doctor_id, is_active, patient_id + is_active, doctor_id + is_active

### med_rep_doctors
**Purpose**: Many-to-many relationship between medical representatives and doctors

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| med_representative_id | bigint | Foreign key to med_representatives |
| doctor_id | bigint | Foreign key to doctors |
| is_active | boolean | Active relationship status |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Constraints**: Unique combination of med_representative_id + doctor_id

**Indexes**: med_representative_id, doctor_id, is_active, med_representative_id + is_active, doctor_id + is_active

## 📋 Additional Medical Tables

### prescriptions
**Purpose**: Detailed prescription management

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| patient_id | bigint | Foreign key to patients |
| doctor_id | bigint | Foreign key to doctors |
| encounter_id | bigint | Foreign key to encounters (nullable) |
| medication_name | varchar(255) | Medication name |
| dosage | varchar(255) | Dosage instructions |
| frequency | varchar(255) | Frequency of administration |
| quantity | int | Quantity prescribed |
| refills | int | Number of refills allowed |
| instructions | text | Special instructions |
| status | enum | active, completed, cancelled |
| start_date | date | Prescription start date |
| end_date | date | Prescription end date |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: medication_name, status, patient_id, doctor_id, start_date, end_date, patient_id + status

### lab_results
**Purpose**: Laboratory test results

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| patient_id | bigint | Foreign key to patients |
| doctor_id | bigint | Foreign key to doctors |
| encounter_id | bigint | Foreign key to encounters (nullable) |
| test_name | varchar(255) | Test name |
| test_date | date | Test date |
| result_date | date | Result date |
| results | text | Test results |
| normal_range | text | Normal range values |
| status | enum | pending, completed, abnormal |
| notes | text | Additional notes |
| lab_name | varchar(255) | Laboratory name |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: test_name, test_date, result_date, status, patient_id, doctor_id, patient_id + test_date

### medication_items
**Purpose**: Medication catalog

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| medication_name | varchar(255) | Medication name |
| dosage | varchar(255) | Standard dosage |
| frequency | varchar(255) | Standard frequency |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: medication_name

## 🔔 System Tables

### notifications
**Purpose**: System notifications

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| type | varchar(255) | Notification type |
| notifiable_type | varchar(255) | Model type |
| notifiable_id | bigint | Model ID |
| data | text | Notification data |
| read_at | timestamp | Read timestamp |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: notifiable_type + notifiable_id, type, read_at

### activities
**Purpose**: Activity logging

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| subject_type | varchar(255) | Subject model type |
| subject_id | bigint | Subject model ID |
| type | varchar(255) | Activity type |
| description | text | Activity description |
| changes | json | Data changes |
| causer_id | bigint | Foreign key to users (nullable) |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: subject_type + subject_id, type, causer_id, created_at

### audit_logs
**Purpose**: Comprehensive audit logging for compliance

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| event | varchar(255) | Event type (created, updated, deleted) |
| auditable_type | varchar(255) | Model type |
| auditable_id | bigint | Model ID |
| old_values | json | Previous values |
| new_values | json | New values |
| url | varchar(255) | Request URL |
| ip_address | varchar(45) | Client IP address |
| user_agent | varchar(255) | Client user agent |
| tags | varchar(255) | Event tags |
| user_id | bigint | Foreign key to users (nullable) |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: auditable_type + auditable_id, event, user_id, created_at, ip_address

### addresses
**Purpose**: Polymorphic address storage

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| label | varchar(255) | Address label |
| default | boolean | Default address flag |
| addressable_type | varchar(255) | Model type |
| addressable_id | bigint | Model ID |
| address_line_1 | varchar(255) | Street address |
| address_line_2 | varchar(255) | Additional address info |
| city | varchar(255) | City |
| state | varchar(255) | State/Province |
| postal_code | varchar(255) | Postal code |
| country | varchar(2) | Country code |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: addressable_type + addressable_id, city, state, postal_code, country

### settings
**Purpose**: System configuration

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| key | varchar(255) | Setting key (unique) |
| value | text | Setting value |

### ads
**Purpose**: Advertisement management

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| title | varchar(255) | Ad title |
| description | text | Ad description |
| image_url | varchar(255) | Ad image URL |
| link_url | varchar(255) | Ad link URL |
| status | boolean | Active status |
| start_date | date | Ad start date |
| end_date | date | Ad end date |
| youtube_id | varchar(255) | YouTube video ID |
| created_at | timestamp | Record creation time |
| updated_at | timestamp | Record update time |

**Indexes**: title, status, start_date, end_date, status + start_date + end_date

## 🔗 Relationships & Foreign Keys

### Primary Relationships

#### User Relationships
- `users` → `patients` (1:1 via user_id)
- `users` → `doctors` (1:1 via user_id)
- `users` → `receptionists` (1:1 via user_id)
- `users` → `med_representatives` (1:1 via user_id)

#### Patient Relationships
- `patients` → `encounters` (1:many)
- `patients` → `medications` (1:many)
- `patients` → `allergies` (1:many)
- `patients` → `family_histories` (1:many)
- `patients` → `immunizations` (1:many)
- `patients` → `physical_examinations` (1:many)
- `patients` → `diagnostic_tests` (1:many)
- `patients` → `medical_conditions` (1:many)
- `patients` → `vitals` (1:many)
- `patients` → `queues` (1:many)
- `patients` → `appointments` (1:many)
- `patients` → `billing_invoices` (1:many)
- `patients` → `prescriptions` (1:many)
- `patients` → `lab_results` (1:many)

#### Doctor Relationships
- `doctors` → `encounters` (1:many)
- `doctors` → `queues` (1:many)
- `doctors` → `appointments` (1:many)
- `doctors` → `billing_invoices` (1:many)
- `doctors` → `prescriptions` (1:many)
- `doctors` → `lab_results` (1:many)
- `doctors` → `receptionists` (1:many)
- `doctors` → `doctor_subscriptions` (1:many)

#### Clinic Relationships
- `clinics` → `queues` (1:many)
- `clinics` → `appointments` (1:many)

#### Encounter Relationships
- `encounters` → `medications` (1:many)
- `encounters` → `billing_invoices` (1:many)
- `encounters` → `prescriptions` (1:many)
- `encounters` → `lab_results` (1:many)

### Many-to-Many Relationships

#### Clinic-Doctor Relationships
- `clinics` ↔ `doctors` (via clinic_doctors table)
- `patients` ↔ `doctors` (via patient_doctors table)
- `med_representatives` ↔ `doctors` (via med_rep_doctors table)

### Polymorphic Relationships

#### Addressable Models
- `addresses` → `patients` (polymorphic)
- `addresses` → `doctors` (polymorphic)
- `addresses` → `clinics` (polymorphic)

#### Notifiable Models
- `notifications` → `users` (polymorphic)
- `notifications` → `patients` (polymorphic)
- `notifications` → `doctors` (polymorphic)

#### Auditable Models
- `audit_logs` → All models (polymorphic)

#### Activity Subject Models
- `activities` → All models (polymorphic)

## 📊 Indexes & Performance

### Performance Strategy

The database schema is optimized for common clinic operations with strategic indexing:

#### Search Optimization
- **Name searches**: Composite indexes on first_name + last_name
- **Contact searches**: Indexes on phone_number fields
- **Medical searches**: Indexes on medical record numbers, license numbers

#### Query Optimization
- **Date-based queries**: Indexes on all date fields for reporting
- **Status filtering**: Indexes on all status fields for filtering
- **Relationship queries**: Foreign key indexes for joins

#### Composite Indexes
- **Patient + Date**: For patient history queries
- **Doctor + Date**: For doctor schedule queries
- **Clinic + Status**: For queue management
- **Status + Date**: For reporting and analytics

### Index Categories

#### Primary Indexes
- All primary keys (automatic)
- All unique constraints (automatic)

#### Foreign Key Indexes
- All foreign key columns for join optimization

#### Search Indexes
- Name fields for patient/doctor searches
- Contact fields for communication
- Medical identifiers for record lookup

#### Business Logic Indexes
- Status fields for filtering active records
- Date fields for scheduling and reporting
- Priority fields for queue management

#### Composite Indexes
- Multi-column indexes for complex queries
- Date + status combinations for reporting
- Patient + doctor combinations for relationships

## 🔒 Data Integrity & Constraints

### Check Constraints

#### Financial Constraints
```sql
-- Billing amounts must be non-negative
subtotal >= 0
tax_amount >= 0
total_amount >= 0
paid_amount >= 0
balance_due >= 0

-- Invoice items must have positive quantities and prices
quantity > 0
unit_price >= 0
total_price >= 0
```

### Unique Constraints

#### Business Uniqueness
- `users.email` - Unique email addresses
- `patients.mrn` - Unique medical record numbers
- `clinics.license_number` - Unique business licenses
- `billing_invoices.invoice_number` - Unique invoice numbers
- `practices.slug` - Unique practice identifiers
- `departments.code` - Unique department codes

#### Relationship Uniqueness
- `clinic_doctors` - Unique clinic + doctor combinations
- `patient_doctors` - Unique patient + doctor combinations
- `med_rep_doctors` - Unique representative + doctor combinations

### Foreign Key Constraints

#### Cascade Behaviors
- **CASCADE ON DELETE**: Child records deleted when parent is deleted
- **CASCADE ON UPDATE**: Child records updated when parent is updated
- **NULL ON DELETE**: Child records set to null when parent is deleted
- **RESTRICT**: Prevents deletion if child records exist

#### Soft Delete Support
- `patients`, `doctors`, `receptionists`, `med_representatives` support soft deletes
- `deleted_at` timestamp tracks deletion
- Indexes on `deleted_at` for efficient soft delete queries

## 💡 Usage Examples

### Common Queries

#### Patient Management
```sql
-- Find active patients by name
SELECT * FROM patients 
WHERE first_name LIKE '%John%' 
AND last_name LIKE '%Doe%' 
AND deleted_at IS NULL;

-- Get patient with medical history
SELECT p.*, e.encounter_date, e.diagnosis
FROM patients p
LEFT JOIN encounters e ON p.id = e.patient_id
WHERE p.id = 1
ORDER BY e.encounter_date DESC;
```

#### Appointment Scheduling
```sql
-- Find available doctor slots
SELECT d.first_name, d.last_name, a.appointment_time
FROM doctors d
LEFT JOIN appointments a ON d.id = a.doctor_id 
AND a.appointment_date = '2024-01-15'
WHERE d.is_active = 1
AND a.id IS NULL;

-- Get today's appointments
SELECT a.*, p.first_name, p.last_name, d.first_name as doctor_name
FROM appointments a
JOIN patients p ON a.patient_id = p.id
JOIN doctors d ON a.doctor_id = d.id
WHERE a.appointment_date = CURDATE()
ORDER BY a.appointment_time;
```

#### Queue Management
```sql
-- Get current queue for clinic
SELECT q.*, p.first_name, p.last_name, d.first_name as doctor_name
FROM queues q
JOIN patients p ON q.patient_id = p.id
LEFT JOIN doctors d ON q.doctor_id = d.id
WHERE q.clinic_id = 1
AND q.status = 'waiting'
ORDER BY q.priority DESC, q.created_at ASC;

-- Update queue status
UPDATE queues 
SET status = 'called', called_at = NOW()
WHERE id = 1;
```

#### Billing Operations
```sql
-- Get outstanding invoices
SELECT bi.*, p.first_name, p.last_name, d.first_name as doctor_name
FROM billing_invoices bi
JOIN patients p ON bi.patient_id = p.id
JOIN doctors d ON bi.doctor_id = d.id
WHERE bi.status IN ('sent', 'overdue')
AND bi.balance_due > 0;

-- Calculate monthly revenue
SELECT 
    DATE_FORMAT(paid_date, '%Y-%m') as month,
    SUM(paid_amount) as revenue
FROM billing_invoices
WHERE status = 'paid'
AND paid_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
GROUP BY DATE_FORMAT(paid_date, '%Y-%m')
ORDER BY month;
```

#### Medical Records
```sql
-- Get patient's complete medical history
SELECT 
    'encounter' as type, encounter_date as date, chief_complaint as description
FROM encounters WHERE patient_id = 1
UNION ALL
SELECT 
    'prescription' as type, start_date as date, medication_name as description
FROM prescriptions WHERE patient_id = 1
UNION ALL
SELECT 
    'lab_result' as type, test_date as date, test_name as description
FROM lab_results WHERE patient_id = 1
ORDER BY date DESC;
```

### Laravel Eloquent Examples

#### Patient Model Usage
```php
// Create a new patient
$patient = Patient::create([
    'first_name' => 'John',
    'last_name' => 'Doe',
    'date_of_birth' => '1990-01-01',
    'gender' => 'male',
    'user_id' => $user->id
]);

// Get patient with relationships
$patient = Patient::with(['encounters', 'allergies', 'vitals'])
    ->find(1);

// Get patient's doctors
$doctors = $patient->doctors()->where('is_active', true)->get();
```

#### Appointment Management
```php
// Create appointment
$appointment = Appointment::create([
    'patient_id' => $patient->id,
    'doctor_id' => $doctor->id,
    'clinic_id' => $clinic->id,
    'appointment_date' => '2024-01-15',
    'appointment_time' => '10:00:00',
    'type' => 'consultation'
]);

// Get doctor's schedule
$schedule = Appointment::where('doctor_id', $doctor->id)
    ->where('appointment_date', '2024-01-15')
    ->where('status', 'scheduled')
    ->orderBy('appointment_time')
    ->get();
```

#### Queue Management
```php
// Add patient to queue
$queue = Queue::create([
    'patient_id' => $patient->id,
    'clinic_id' => $clinic->id,
    'doctor_id' => $doctor->id,
    'queue_number' => $nextQueueNumber,
    'priority' => 'normal'
]);

// Get current queue
$currentQueue = Queue::with(['patient', 'doctor'])
    ->where('clinic_id', $clinic->id)
    ->where('status', 'waiting')
    ->orderBy('priority', 'desc')
    ->orderBy('created_at')
    ->get();
```

## 🚀 Migration Commands

### Running the Migration

```bash
# Run the migration
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback if needed
php artisan migrate:rollback

# Fresh migration (drops all tables and re-runs)
php artisan migrate:fresh

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### Database Seeding

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=ClinicSettingsSeeder

# Fresh migration with seeding
php artisan migrate:fresh --seed
```

### Maintenance Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

## 📝 Notes

### Model Considerations

Some models have `public $timestamps = FALSE` which means they don't use Laravel's automatic timestamp management:
- `Patient`
- `Doctor` 
- `Encounter`
- `Medication`

These models handle timestamps manually or don't require them for their specific use cases.

### Performance Considerations

- **Large tables**: Consider partitioning for `audit_logs` and `activities` tables
- **Index maintenance**: Monitor index usage and remove unused indexes
- **Query optimization**: Use EXPLAIN to analyze query performance
- **Connection pooling**: Configure database connection pooling for high traffic

### Security Considerations

- **Audit logging**: All sensitive operations are logged in `audit_logs`
- **Soft deletes**: Sensitive data is soft-deleted rather than permanently removed
- **Data encryption**: Consider encrypting sensitive fields like SSN, medical records
- **Access control**: Implement proper role-based access control

### Backup Strategy

- **Regular backups**: Daily automated backups of all tables
- **Point-in-time recovery**: Enable binary logging for point-in-time recovery
- **Test restores**: Regularly test backup restoration procedures
- **Offsite storage**: Store backups in secure offsite locations

---

This documentation provides a comprehensive overview of the clinic management system's database schema. For specific implementation details, refer to the Laravel models and controllers in the application codebase.

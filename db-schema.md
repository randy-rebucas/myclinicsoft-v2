Designing a database schema for storing medical history requires careful consideration of various factors such as data integrity, security, scalability, and ease of querying. Below is a simplified example of a medical history database schema:

1. **Patients Table:**
   - PatientID (Primary Key)
   - FirstName
   - LastName
   - DateOfBirth
   - Gender
   - ContactInfo
   - OtherDemographicInfo

2. **MedicalConditions Table:**
   - ConditionID (Primary Key)
   - PatientID (Foreign Key referencing Patients table)
   - ConditionName
   - DiagnosisDate
   - Status (e.g., Active, Inactive)
   - TreatmentPlan
   - Notes

3. **Medications Table:**
   - MedicationID (Primary Key)
   - PatientID (Foreign Key referencing Patients table)
   - MedicationName
   - Dosage
   - Frequency
   - StartDate
   - EndDate
   - Notes

4. **Allergies Table:**
   - AllergyID (Primary Key)
   - PatientID (Foreign Key referencing Patients table)
   - Allergen
   - Reaction
   - Severity
   - Notes

5. **FamilyHistory Table:**
   - FamilyHistoryID (Primary Key)
   - PatientID (Foreign Key referencing Patients table)
   - Relationship (e.g., Parent, Sibling)
   - Condition
   - Notes

6. **Immunizations Table:**
   - ImmunizationID (Primary Key)
   - PatientID (Foreign Key referencing Patients table)
   - VaccineName
   - DateAdministered
   - Administrator (e.g., Physician, Nurse)
   - Notes

7. **Encounters Table:** (For recording visits/encounters)
   - EncounterID (Primary Key)
   - PatientID (Foreign Key referencing Patients table)
   - EncounterDate
   - ChiefComplaint
   - Notes

8. **PhysicalExamination Table:**
   - ExamID (Primary Key)
   - EncounterID (Foreign Key referencing Encounters table)
   - VitalSigns (e.g., Blood pressure, Heart rate)
   - GeneralAppearance
   - SystematicFindings
   - Notes

9. **DiagnosticTests Table:**
   - TestID (Primary Key)
   - EncounterID (Foreign Key referencing Encounters table)
   - TestName
   - TestDate
   - Results
   - Notes

This schema provides a basic structure for storing essential medical history data. Depending on specific requirements, additional tables or fields may be necessary. It's also important to implement appropriate indexing, constraints, and relationships to ensure data integrity and optimize query performance. Additionally, proper security measures should be implemented to protect sensitive patient information.
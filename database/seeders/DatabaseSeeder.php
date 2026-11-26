<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\Client;
use App\Models\Site;
use App\Models\Guard;
use App\Models\GuardDocument;
use App\Models\Deployment;
use App\Models\DutyRoster;
use App\Models\Attendance;
use App\Models\AttendanceAudit;
use App\Models\Leave;
use App\Models\SalaryAdvance;
use App\Models\Payroll;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\EquipmentItem;
use App\Models\EquipmentAssignment;
use App\Models\Incident;
use App\Models\Expense;
use App\Models\AuditLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::today();

        // 1. Users
        $admin = User::create([
            'name' => 'Agency Administrator',
            'email' => 'admin@webotapp.com',
            'phone' => '+91 98640 12345',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $hr = User::create([
            'name' => 'Priyanka Sharma',
            'email' => 'hr@webotapp.com',
            'phone' => '+91 98640 54321',
            'role' => 'hr_manager',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $supervisor = User::create([
            'name' => 'Ramesh Bordoloi',
            'email' => 'supervisor@webotapp.com',
            'phone' => '+91 98640 98765',
            'role' => 'site_supervisor',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $accountant = User::create([
            'name' => 'Vikram Singhania',
            'email' => 'accounts@webotapp.com',
            'phone' => '+91 98640 67890',
            'role' => 'accountant',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $operations = User::create([
            'name' => 'Anil Kalita',
            'email' => 'operations@webotapp.com',
            'phone' => '+91 98640 11223',
            'role' => 'operations_manager',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        // 2. Business Settings
        $settingsData = [
            'agency_name' => 'Garuda Apex Security Services Pvt. Ltd.',
            'agency_tagline' => 'Vigilant, Disciplined & Reliable Security Solutions',
            'agency_phone' => '+91 98640 12345',
            'agency_email' => 'contact@garudasecurity.in',
            'agency_address' => 'Plot 42, G.S. Road, Christian Basti, Guwahati, Assam - 781005',
            'agency_gstin' => '18AABCG1234F1Z8',
            'invoice_prefix' => 'GSS-INV',
            'invoice_footer' => 'Thank you for choosing Garuda Apex Security Services. For billing inquiries, email accounts@garudasecurity.in.',
            'currency_symbol' => '₹',
            'currency_code' => 'INR',
            'default_shift_hours' => '8',
            'overtime_multiplier' => '1.5',
            'pf_applicable' => '1',
            'esi_applicable' => '1',
        ];

        foreach ($settingsData as $k => $v) {
            Setting::set($k, $v, 'business');
        }

        // 3. Shifts
        $shiftMorn = Shift::create([
            'name' => 'Morning Shift',
            'code' => 'MORN',
            'start_time' => '06:00:00',
            'end_time' => '14:00:00',
            'duration_hours' => 8.00,
            'is_active' => true,
        ]);

        $shiftEven = Shift::create([
            'name' => 'Evening Shift',
            'code' => 'EVEN',
            'start_time' => '14:00:00',
            'end_time' => '22:00:00',
            'duration_hours' => 8.00,
            'is_active' => true,
        ]);

        $shiftNigh = Shift::create([
            'name' => 'Night Shift',
            'code' => 'NIGH',
            'start_time' => '22:00:00',
            'end_time' => '06:00:00',
            'duration_hours' => 8.00,
            'is_active' => true,
        ]);

        $shiftGen = Shift::create([
            'name' => 'General Day Shift',
            'code' => 'GEN',
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
            'duration_hours' => 9.00,
            'is_active' => true,
        ]);

        // 4. Clients
        $client1 = Client::create([
            'client_code' => 'CLI-001',
            'client_name' => 'Brahmaputra Tech Park Ltd.',
            'company_name' => 'Brahmaputra Infrastructure & Tech Parks Ltd.',
            'contact_person' => 'Mr. Manash Barua (Facility Head)',
            'phone' => '+91 94350 11223',
            'email' => 'facilities@brahmaputratech.in',
            'billing_address' => 'IT Park Phase II, Borjhar Industrial Area, Guwahati, Assam - 781015',
            'gstin' => '18AAACB1122P1Z3',
            'state' => 'Assam',
            'pincode' => '781015',
            'contract_start_date' => Carbon::parse('2026-01-01'),
            'contract_end_date' => Carbon::parse('2026-12-31'),
            'contract_amount' => 1250000.00,
            'payment_terms' => 'Net 30',
            'status' => 'active',
            'notes' => 'Priority IT infrastructure client. 24x7 gate security and baggage scanning required.',
        ]);

        $client2 = Client::create([
            'client_code' => 'CLI-002',
            'client_name' => 'Brahmaputra Heights RWA',
            'company_name' => 'Brahmaputra Heights Cooperative Housing Society',
            'contact_person' => 'Col. H. N. Gogoi (Retd., President)',
            'phone' => '+91 94351 44556',
            'email' => 'management@brahmaputraheights.org',
            'billing_address' => 'Zoo Road Tiniali, Guwahati, Assam - 781024',
            'gstin' => '18AAACB3344M1Z5',
            'state' => 'Assam',
            'pincode' => '781024',
            'contract_start_date' => Carbon::parse('2026-04-01'),
            'contract_end_date' => Carbon::parse('2027-03-31'),
            'contract_amount' => 720000.00,
            'payment_terms' => 'Net 15',
            'status' => 'active',
            'notes' => 'Residential apartment with 220 flats. Visitor log and boom barrier management.',
        ]);

        $client3 = Client::create([
            'client_code' => 'CLI-003',
            'client_name' => 'Kamakhya Mega Warehousing',
            'company_name' => 'Kamakhya Logistics & Supply Chain Pvt. Ltd.',
            'contact_person' => 'Deepak Roy (Operations VP)',
            'phone' => '+91 94352 77889',
            'email' => 'security@kamakhyalogistics.com',
            'billing_address' => 'NH 27 Highway Yard, Changsari, Kamrup, Assam - 781101',
            'gstin' => '18AAACK5566L1Z9',
            'state' => 'Assam',
            'pincode' => '781101',
            'contract_start_date' => Carbon::parse('2026-02-15'),
            'contract_end_date' => Carbon::parse('2026-12-15'), // Expiring soon alert!
            'contract_amount' => 980000.00,
            'payment_terms' => 'Net 30',
            'status' => 'active',
            'notes' => 'High-value warehouse facility. Heavy vehicle gate passes and seal inspections.',
        ]);

        $client4 = Client::create([
            'client_code' => 'CLI-004',
            'client_name' => 'Silver Square Commercial Mall',
            'company_name' => 'Silver Square Retail Ventures Ltd.',
            'contact_person' => 'Neha Agarwal (Mall Manager)',
            'phone' => '+91 94353 99001',
            'email' => 'admin@silversquaremall.com',
            'billing_address' => 'Christian Basti, G.S. Road, Guwahati, Assam - 781005',
            'gstin' => '18AAACS7788K1Z2',
            'state' => 'Assam',
            'pincode' => '781005',
            'contract_start_date' => Carbon::parse('2026-03-01'),
            'contract_end_date' => Carbon::parse('2027-02-28'),
            'contract_amount' => 1440000.00,
            'payment_terms' => 'Net 15',
            'status' => 'active',
            'notes' => 'Retail mall with cinema and food court. Metal detectors and emergency response drills.',
        ]);

        // 5. Sites
        $site1 = Site::create([
            'client_id' => $client1->id,
            'site_code' => 'STE-001',
            'site_name' => 'Tech Park Tower A & Gate 1',
            'site_type' => 'Office',
            'address' => 'IT Park Phase II, Borjhar Industrial Area, Guwahati',
            'contact_person' => 'Suraj Dutta (Facility Manager)',
            'contact_phone' => '+91 94350 55441',
            'required_guards' => 3,
            'supervisor_id' => $supervisor->id,
            'duty_instructions' => 'Mandatory RFID badge check at turnstiles. Strict vendor verification and vehicle pass scanning.',
            'shift_requirements' => '2 Morning, 2 Evening, 1 Night shift',
            'status' => 'active',
        ]);

        $site2 = Site::create([
            'client_id' => $client2->id,
            'site_code' => 'STE-002',
            'site_name' => 'Brahmaputra Heights Main Gate & Clubhouse',
            'site_type' => 'Residential apartment',
            'address' => 'Zoo Road Tiniali, Guwahati',
            'contact_person' => 'N. C. Das (Society Caretaker)',
            'contact_phone' => '+91 94351 22334',
            'required_guards' => 2,
            'supervisor_id' => $supervisor->id,
            'duty_instructions' => 'Maintain visitor register. Check delivery personnel OTP before granting entry.',
            'shift_requirements' => '1 Morning, 1 Evening, 1 Night shift',
            'status' => 'active',
        ]);

        $site3 = Site::create([
            'client_id' => $client3->id,
            'site_code' => 'STE-003',
            'site_name' => 'Changsari Depot Yard 3',
            'site_type' => 'Warehouse',
            'address' => 'NH 27 Highway Yard, Changsari, Kamrup',
            'contact_person' => 'Bikash Mahanta (Depot Incharge)',
            'contact_phone' => '+91 94352 66778',
            'required_guards' => 3,
            'supervisor_id' => $supervisor->id,
            'duty_instructions' => 'Check container container seal numbers against manifest. Night perimeter patrol every 45 mins.',
            'shift_requirements' => '1 General, 1 Evening, 2 Night shift',
            'status' => 'active',
        ]);

        $site4 = Site::create([
            'client_id' => $client4->id,
            'site_code' => 'STE-004',
            'site_name' => 'Silver Square North Atrium & Parking Gate',
            'site_type' => 'Shopping mall',
            'address' => 'Christian Basti, G.S. Road, Guwahati',
            'contact_person' => 'Sunil Choudhury (Operations Head)',
            'contact_phone' => '+91 94353 11889',
            'required_guards' => 4,
            'supervisor_id' => $supervisor->id,
            'duty_instructions' => 'Door-frame metal detector monitoring. Crowd management near escalator and cinema exit.',
            'shift_requirements' => '2 Morning, 2 Evening, 1 Night shift',
            'status' => 'active',
        ]);

        // 6. Guards
        $guardsData = [
            [
                'guard_code' => 'SEC-G-001',
                'full_name' => 'Bhaben Chandra Nath',
                'father_name' => 'Dharani Nath',
                'dob' => '1989-05-14',
                'gender' => 'Male',
                'phone' => '+91 98541 23401',
                'alternate_phone' => '+91 94350 99881',
                'email' => 'bhaben.nath@example.com',
                'permanent_address' => 'Vill- Palashbari, P.O.- Palashbari, Dist- Kamrup Rural, Assam 781128',
                'current_address' => 'House No 14, Borjhar Tiniali, Guwahati 781015',
                'emergency_contact_name' => 'Runumi Nath (Wife)',
                'emergency_contact_phone' => '+91 98541 99002',
                'aadhaar_reference' => '784512984512',
                'pan_number' => 'ABCDE1234F',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/KMP/2025/0891',
                'background_verification_status' => 'verified',
                'joining_date' => '2024-03-01',
                'employment_type' => 'full_time',
                'designation' => 'Head Guard',
                'qualification' => 'Higher Secondary (12th Passed)',
                'training_details' => 'Ex-Home Guard, PSARA Certified 160 hrs Security Training completed at ATDC Guwahati',
                'experience_years' => '6 Years',
                'blood_group' => 'B+',
                'bank_name' => 'State Bank of India',
                'account_holder_name' => 'Bhaben Chandra Nath',
                'account_number' => '30987654321',
                'ifsc_code' => 'SBIN0001234',
                'salary_type' => 'monthly',
                'basic_salary' => 19500.00,
                'allowances' => 2500.00,
                'overtime_rate' => 120.00,
                'status' => 'active',
                'notes' => 'Very reliable, disciplined. Holds valid basic fire fighting certification.',
            ],
            [
                'guard_code' => 'SEC-G-002',
                'full_name' => 'Dhrubajyoti Deka',
                'father_name' => 'Kandarpa Deka',
                'dob' => '1994-08-22',
                'gender' => 'Male',
                'phone' => '+91 98541 23402',
                'alternate_phone' => null,
                'email' => 'dhruba.deka@example.com',
                'permanent_address' => 'Vill- Nalbari Town, Ward 4, Nalbari, Assam 781335',
                'current_address' => 'Brahmaputra Heights Guard Quarters, Zoo Road, Guwahati',
                'emergency_contact_name' => 'Kandarpa Deka (Father)',
                'emergency_contact_phone' => '+91 98541 99003',
                'aadhaar_reference' => '901234567890',
                'pan_number' => 'BCDEF2345G',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/NLB/2025/1102',
                'background_verification_status' => 'verified',
                'joining_date' => '2024-06-15',
                'employment_type' => 'full_time',
                'designation' => 'Security Guard',
                'qualification' => 'HSLC (10th Passed)',
                'training_details' => 'PSARA Standard Guard Induction & CCTV Basic Monitoring Course',
                'experience_years' => '3 Years',
                'blood_group' => 'O+',
                'bank_name' => 'Punjab National Bank',
                'account_holder_name' => 'Dhrubajyoti Deka',
                'account_number' => '1234000100098765',
                'ifsc_code' => 'PUNB0123400',
                'salary_type' => 'monthly',
                'basic_salary' => 16500.00,
                'allowances' => 1500.00,
                'overtime_rate' => 100.00,
                'status' => 'active',
                'notes' => 'Good physical fitness and polite demeanor with residents.',
            ],
            [
                'guard_code' => 'SEC-G-003',
                'full_name' => 'Manoj Kumar Rabha',
                'father_name' => 'Binod Rabha',
                'dob' => '1991-11-03',
                'gender' => 'Male',
                'phone' => '+91 98541 23403',
                'alternate_phone' => '+91 97060 11224',
                'email' => 'manoj.rabha@example.com',
                'permanent_address' => 'Vill- Boko, Goalpara Road, Kamrup, Assam 781123',
                'current_address' => 'Jalukbari Tiniali, Guwahati 781014',
                'emergency_contact_name' => 'Binod Rabha (Father)',
                'emergency_contact_phone' => '+91 97060 11225',
                'aadhaar_reference' => '456789012345',
                'pan_number' => 'CDEFG3456H',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/KMP/2025/1440',
                'background_verification_status' => 'verified',
                'joining_date' => '2024-08-01',
                'employment_type' => 'full_time',
                'designation' => 'Armed Gunman',
                'qualification' => '12th Passed + NCC B Certificate',
                'training_details' => 'Retd. Assam Rifles (7 Years service), Valid 12 Bore DBBL Gun License verified',
                'experience_years' => '8 Years',
                'blood_group' => 'A+',
                'bank_name' => 'State Bank of India',
                'account_holder_name' => 'Manoj Kumar Rabha',
                'account_number' => '31223344556',
                'ifsc_code' => 'SBIN0004567',
                'salary_type' => 'monthly',
                'basic_salary' => 24000.00,
                'allowances' => 3000.00,
                'overtime_rate' => 150.00,
                'status' => 'active',
                'notes' => 'Armed guard deployed at logistics warehouse hub. Weapon inspected quarterly.',
            ],
            [
                'guard_code' => 'SEC-G-004',
                'full_name' => 'Rina Begum',
                'father_name' => 'Abdul Rashid',
                'dob' => '1996-03-18',
                'gender' => 'Female',
                'phone' => '+91 98541 23404',
                'alternate_phone' => null,
                'email' => 'rina.begum@example.com',
                'permanent_address' => 'Ward 2, Mangaldai Town, Darrang, Assam 784125',
                'current_address' => 'Christian Basti Rental Apartment, Guwahati 781005',
                'emergency_contact_name' => 'Abdul Rashid (Father)',
                'emergency_contact_phone' => '+91 98541 99005',
                'aadhaar_reference' => '567890123456',
                'pan_number' => 'DEFGH4567I',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/DRG/2025/0312',
                'background_verification_status' => 'verified',
                'joining_date' => '2025-01-10',
                'employment_type' => 'full_time',
                'designation' => 'Lady Guard',
                'qualification' => 'Graduate (BA)',
                'training_details' => 'Female Frisking, Customer Hospitality & Emergency First Aid certified',
                'experience_years' => '2.5 Years',
                'blood_group' => 'AB+',
                'bank_name' => 'HDFC Bank',
                'account_holder_name' => 'Rina Begum',
                'account_number' => '50100234567890',
                'ifsc_code' => 'HDFC0001234',
                'salary_type' => 'monthly',
                'basic_salary' => 17500.00,
                'allowances' => 1500.00,
                'overtime_rate' => 110.00,
                'status' => 'active',
                'notes' => 'Stationed at Silver Square Mall female baggage and entry check point.',
            ],
            [
                'guard_code' => 'SEC-G-005',
                'full_name' => 'Pranjal Saikia',
                'father_name' => 'Horen Saikia',
                'dob' => '1992-07-29',
                'gender' => 'Male',
                'phone' => '+91 98541 23405',
                'alternate_phone' => '+91 94350 33445',
                'email' => 'pranjal.saikia@example.com',
                'permanent_address' => 'Vill- Morigaon Town, Dist- Morigaon, Assam 782105',
                'current_address' => 'Six Mile Panjabari Road, Guwahati 781022',
                'emergency_contact_name' => 'Jonali Saikia (Wife)',
                'emergency_contact_phone' => '+91 94350 33446',
                'aadhaar_reference' => '678901234567',
                'pan_number' => 'EFGHI5678J',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/MOR/2025/0987',
                'background_verification_status' => 'verified',
                'joining_date' => '2024-05-01',
                'employment_type' => 'full_time',
                'designation' => 'Security Guard',
                'qualification' => '10th Passed',
                'training_details' => 'Industrial Security & Fire Fighting Drill passed',
                'experience_years' => '4 Years',
                'blood_group' => 'O-',
                'bank_name' => 'State Bank of India',
                'account_holder_name' => 'Pranjal Saikia',
                'account_number' => '32889900112',
                'ifsc_code' => 'SBIN0007890',
                'salary_type' => 'monthly',
                'basic_salary' => 16500.00,
                'allowances' => 1500.00,
                'overtime_rate' => 100.00,
                'status' => 'active',
                'notes' => 'Punctual, steady night shift operator at Tech Park.',
            ],
            [
                'guard_code' => 'SEC-G-006',
                'full_name' => 'Bikash Chamuah',
                'father_name' => 'Biren Chamuah',
                'dob' => '1995-12-11',
                'gender' => 'Male',
                'phone' => '+91 98541 23406',
                'alternate_phone' => null,
                'email' => 'bikash.chamuah@example.com',
                'permanent_address' => 'Teok, Jorhat, Assam 785112',
                'current_address' => 'Dispur Super Market Colony, Guwahati 781006',
                'emergency_contact_name' => 'Biren Chamuah (Father)',
                'emergency_contact_phone' => '+91 98541 99007',
                'aadhaar_reference' => '789012345678',
                'pan_number' => 'FGHIJ6789K',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/JRT/2025/0421',
                'background_verification_status' => 'verified',
                'joining_date' => '2024-09-01',
                'employment_type' => 'full_time',
                'designation' => 'Security Guard',
                'qualification' => '12th Passed',
                'training_details' => 'PSARA security course & Boom Barrier vehicle access management',
                'experience_years' => '3 Years',
                'blood_group' => 'B+',
                'bank_name' => 'Axis Bank',
                'account_holder_name' => 'Bikash Chamuah',
                'account_number' => '918010045678901',
                'ifsc_code' => 'UTIB0000456',
                'salary_type' => 'monthly',
                'basic_salary' => 16500.00,
                'allowances' => 1500.00,
                'overtime_rate' => 100.00,
                'status' => 'active',
                'notes' => 'Stationed at Brahmaputra Heights gate.',
            ],
            [
                'guard_code' => 'SEC-G-007',
                'full_name' => 'Tapan Hazarika',
                'father_name' => 'Mohendra Hazarika',
                'dob' => '1990-04-05',
                'gender' => 'Male',
                'phone' => '+91 98541 23407',
                'alternate_phone' => null,
                'email' => 'tapan.hazarika@example.com',
                'permanent_address' => 'Sipajhar, Darrang, Assam 784145',
                'current_address' => 'Changsari Truck Terminal Line, Kamrup 781101',
                'emergency_contact_name' => 'Mamoni Hazarika (Sister)',
                'emergency_contact_phone' => '+91 98541 99008',
                'aadhaar_reference' => '890123456789',
                'pan_number' => 'GHIJK7890L',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/DRG/2025/0819',
                'background_verification_status' => 'verified',
                'joining_date' => '2024-10-15',
                'employment_type' => 'full_time',
                'designation' => 'Security Guard',
                'qualification' => '10th Passed',
                'training_details' => 'Night patrol, perimeter inspection and torch signaling',
                'experience_years' => '4 Years',
                'blood_group' => 'A+',
                'bank_name' => 'Canara Bank',
                'account_holder_name' => 'Tapan Hazarika',
                'account_number' => '2049101012345',
                'ifsc_code' => 'CNRB0002049',
                'salary_type' => 'monthly',
                'basic_salary' => 16500.00,
                'allowances' => 1500.00,
                'overtime_rate' => 100.00,
                'status' => 'active',
                'notes' => 'Strong vigilance during night shifts at logistics depot.',
            ],
            [
                'guard_code' => 'SEC-G-008',
                'full_name' => 'Dipankar Das',
                'father_name' => 'Sunil Das',
                'dob' => '1997-09-17',
                'gender' => 'Male',
                'phone' => '+91 98541 23408',
                'alternate_phone' => null,
                'email' => 'dipankar.das@example.com',
                'permanent_address' => 'Sualkuchi, Kamrup Rural, Assam 781103',
                'current_address' => 'Lalganesh Main Road, Guwahati 781034',
                'emergency_contact_name' => 'Sunil Das (Father)',
                'emergency_contact_phone' => '+91 98541 99009',
                'aadhaar_reference' => '901234567891',
                'pan_number' => 'HIJKL8901M',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/KMP/2025/1990',
                'background_verification_status' => 'verified',
                'joining_date' => '2025-02-01',
                'employment_type' => 'full_time',
                'designation' => 'Security Guard',
                'qualification' => '12th Passed',
                'training_details' => 'Mall security protocol & parking ticketing systems',
                'experience_years' => '1.5 Years',
                'blood_group' => 'O+',
                'bank_name' => 'State Bank of India',
                'account_holder_name' => 'Dipankar Das',
                'account_number' => '39887766554',
                'ifsc_code' => 'SBIN0003010',
                'salary_type' => 'monthly',
                'basic_salary' => 16500.00,
                'allowances' => 1500.00,
                'overtime_rate' => 100.00,
                'status' => 'active',
                'notes' => 'Active at Silver Square Mall parking entry.',
            ],
            [
                'guard_code' => 'SEC-G-009',
                'full_name' => 'Sanjay Boro',
                'father_name' => 'Lalit Boro',
                'dob' => '1993-02-14',
                'gender' => 'Male',
                'phone' => '+91 98541 23409',
                'alternate_phone' => null,
                'email' => 'sanjay.boro@example.com',
                'permanent_address' => 'Tamulpur, Baksa, BTR, Assam 781367',
                'current_address' => 'Beltola Tiniali, Guwahati 781028',
                'emergency_contact_name' => 'Lalit Boro (Father)',
                'emergency_contact_phone' => '+91 98541 99010',
                'aadhaar_reference' => '012345678912',
                'pan_number' => 'IJKLM9012N',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/BKS/2025/0122',
                'background_verification_status' => 'verified',
                'joining_date' => '2024-11-01',
                'employment_type' => 'full_time',
                'designation' => 'Security Guard',
                'qualification' => '10th Passed',
                'training_details' => 'Physical drill and emergency evacuation training',
                'experience_years' => '3.5 Years',
                'blood_group' => 'B+',
                'bank_name' => 'Assam Gramin Vikash Bank',
                'account_holder_name' => 'Sanjay Boro',
                'account_number' => '70012345678',
                'ifsc_code' => 'PUNB0RRBAGB',
                'salary_type' => 'monthly',
                'basic_salary' => 16500.00,
                'allowances' => 1500.00,
                'overtime_rate' => 100.00,
                'status' => 'active',
                'notes' => 'Available for rotation and replacement duties.',
            ],
            [
                'guard_code' => 'SEC-G-010',
                'full_name' => 'Deepak Chetri',
                'father_name' => 'Prem Chetri',
                'dob' => '1991-06-25',
                'gender' => 'Male',
                'phone' => '+91 98541 23410',
                'alternate_phone' => null,
                'email' => 'deepak.chetri@example.com',
                'permanent_address' => 'Digboi, Tinsukia, Assam 786171',
                'current_address' => 'Hatigaon Bhetapara, Guwahati 781038',
                'emergency_contact_name' => 'Prem Chetri (Father)',
                'emergency_contact_phone' => '+91 98541 99011',
                'aadhaar_reference' => '123456789123',
                'pan_number' => 'JKLMN0123O',
                'police_verification_status' => 'pending',
                'police_verification_ref' => 'PV/TSK/REQ-778',
                'background_verification_status' => 'verified',
                'joining_date' => '2025-05-10',
                'employment_type' => 'contract',
                'designation' => 'Security Guard',
                'qualification' => '10th Passed',
                'training_details' => 'Industrial security basics',
                'experience_years' => '2 Years',
                'blood_group' => 'AB-',
                'bank_name' => 'State Bank of India',
                'account_holder_name' => 'Deepak Chetri',
                'account_number' => '33221100445',
                'ifsc_code' => 'SBIN0001889',
                'salary_type' => 'monthly',
                'basic_salary' => 16000.00,
                'allowances' => 1000.00,
                'overtime_rate' => 100.00,
                'status' => 'active',
                'notes' => 'Currently available in reserve pool.',
            ],
            [
                'guard_code' => 'SEC-G-011',
                'full_name' => 'Mukesh Kalita',
                'father_name' => 'Gajen Kalita',
                'dob' => '1987-10-10',
                'gender' => 'Male',
                'phone' => '+91 98541 23411',
                'alternate_phone' => null,
                'email' => 'mukesh.kalita@example.com',
                'permanent_address' => 'Hajo Town, Kamrup, Assam 781102',
                'current_address' => 'Hajo Town, Kamrup 781102',
                'emergency_contact_name' => 'Gajen Kalita (Father)',
                'emergency_contact_phone' => '+91 98541 99012',
                'aadhaar_reference' => '234567891234',
                'pan_number' => 'KLMNO1234P',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/KMP/2024/0912',
                'background_verification_status' => 'verified',
                'joining_date' => '2023-01-15',
                'employment_type' => 'full_time',
                'designation' => 'Field Officer',
                'qualification' => 'Graduate (B.Sc)',
                'training_details' => 'Supervisor & Security Audit Certified by APSSA',
                'experience_years' => '9 Years',
                'blood_group' => 'O+',
                'bank_name' => 'HDFC Bank',
                'account_holder_name' => 'Mukesh Kalita',
                'account_number' => '50100987654321',
                'ifsc_code' => 'HDFC0000456',
                'salary_type' => 'monthly',
                'basic_salary' => 28000.00,
                'allowances' => 4500.00,
                'overtime_rate' => 160.00,
                'status' => 'active',
                'notes' => 'Field Officer conducting random spot-checks and night supervision.',
            ],
            [
                'guard_code' => 'SEC-G-012',
                'full_name' => 'Hemanta Barman',
                'father_name' => 'Deben Barman',
                'dob' => '1995-01-08',
                'gender' => 'Male',
                'phone' => '+91 98541 23412',
                'alternate_phone' => null,
                'email' => 'hemanta.barman@example.com',
                'permanent_address' => 'Tihu Town, Barpeta, Assam 781371',
                'current_address' => 'Ulubari, Guwahati 781007',
                'emergency_contact_name' => 'Deben Barman (Father)',
                'emergency_contact_phone' => '+91 98541 99013',
                'aadhaar_reference' => '345678912345',
                'pan_number' => 'LMNOP2345Q',
                'police_verification_status' => 'verified',
                'police_verification_ref' => 'PV/BRP/2024/0554',
                'background_verification_status' => 'verified',
                'joining_date' => '2024-04-10',
                'employment_type' => 'full_time',
                'designation' => 'Security Guard',
                'qualification' => '10th Passed',
                'training_details' => 'PSARA certified guard',
                'experience_years' => '2 Years',
                'blood_group' => 'A+',
                'bank_name' => 'State Bank of India',
                'account_holder_name' => 'Hemanta Barman',
                'account_number' => '31445566778',
                'ifsc_code' => 'SBIN0001010',
                'salary_type' => 'monthly',
                'basic_salary' => 16500.00,
                'allowances' => 1500.00,
                'overtime_rate' => 100.00,
                'status' => 'resigned',
                'notes' => 'Resigned on personal grounds in October 2026.',
            ],
        ];

        $createdGuards = [];
        foreach ($guardsData as $gData) {
            $guard = Guard::create($gData);
            $createdGuards[$guard->guard_code] = $guard;

            // Add standard document record
            GuardDocument::create([
                'guard_id' => $guard->id,
                'document_type' => 'aadhaar',
                'document_title' => 'Aadhaar Card Copy',
                'document_number' => $guard->masked_aadhaar,
                'file_path' => 'documents/sample_aadhaar.pdf',
            ]);

            GuardDocument::create([
                'guard_id' => $guard->id,
                'document_type' => 'police_verification',
                'document_title' => 'Police Clearance Certificate',
                'document_number' => $guard->police_verification_ref,
                'file_path' => 'documents/sample_police_verification.pdf',
                'expiry_date' => Carbon::now()->addYear(),
            ]);
        }

        // 7. Deployments (Non-overlapping, strictly valid)
        // Site 1 (Tech Park): G-001 (MORN), G-005 (NIGH)
        $dep1 = Deployment::create([
            'guard_id' => $createdGuards['SEC-G-001']->id,
            'site_id' => $site1->id,
            'shift_id' => $shiftMorn->id,
            'designation' => 'Head Guard',
            'start_date' => Carbon::parse('2026-06-01'),
            'duty_instructions' => 'Oversee turnstiles and visitor logging',
            'status' => 'active',
        ]);

        $dep2 = Deployment::create([
            'guard_id' => $createdGuards['SEC-G-005']->id,
            'site_id' => $site1->id,
            'shift_id' => $shiftNigh->id,
            'designation' => 'Security Guard',
            'start_date' => Carbon::parse('2026-07-01'),
            'duty_instructions' => 'Night shift surveillance and server room checks',
            'status' => 'active',
        ]);

        // Site 2 (Brahmaputra Heights): G-002 (MORN), G-006 (EVEN)
        $dep3 = Deployment::create([
            'guard_id' => $createdGuards['SEC-G-002']->id,
            'site_id' => $site2->id,
            'shift_id' => $shiftMorn->id,
            'designation' => 'Security Guard',
            'start_date' => Carbon::parse('2026-06-15'),
            'duty_instructions' => 'Main gate entry log and delivery vehicle checks',
            'status' => 'active',
        ]);

        $dep4 = Deployment::create([
            'guard_id' => $createdGuards['SEC-G-006']->id,
            'site_id' => $site2->id,
            'shift_id' => $shiftEven->id,
            'designation' => 'Security Guard',
            'start_date' => Carbon::parse('2026-07-15'),
            'duty_instructions' => 'Evening boom barrier operation & parking checks',
            'status' => 'active',
        ]);

        // Site 3 (Kamakhya Logistics): G-003 (GEN), G-007 (NIGH)
        $dep5 = Deployment::create([
            'guard_id' => $createdGuards['SEC-G-003']->id,
            'site_id' => $site3->id,
            'shift_id' => $shiftGen->id,
            'designation' => 'Armed Gunman',
            'start_date' => Carbon::parse('2026-08-01'),
            'duty_instructions' => 'Armed security at cash desk & high-value electronics bay',
            'status' => 'active',
        ]);

        $dep6 = Deployment::create([
            'guard_id' => $createdGuards['SEC-G-007']->id,
            'site_id' => $site3->id,
            'shift_id' => $shiftNigh->id,
            'designation' => 'Security Guard',
            'start_date' => Carbon::parse('2026-08-01'),
            'duty_instructions' => 'Perimeter fence checks and outbound truck pass verification',
            'status' => 'active',
        ]);

        // Site 4 (Silver Square Mall): G-004 (MORN), G-008 (EVEN)
        $dep7 = Deployment::create([
            'guard_id' => $createdGuards['SEC-G-004']->id,
            'site_id' => $site4->id,
            'shift_id' => $shiftMorn->id,
            'designation' => 'Lady Guard',
            'start_date' => Carbon::parse('2026-08-10'),
            'duty_instructions' => 'North atrium female frisking & customer assistance',
            'status' => 'active',
        ]);

        $dep8 = Deployment::create([
            'guard_id' => $createdGuards['SEC-G-008']->id,
            'site_id' => $site4->id,
            'shift_id' => $shiftEven->id,
            'designation' => 'Security Guard',
            'start_date' => Carbon::parse('2026-08-10'),
            'duty_instructions' => 'Basement parking entry and traffic flow control',
            'status' => 'active',
        ]);

        // 8. Duty Roster for Today
        $rosterPairs = [
            [$site1->id, $createdGuards['SEC-G-001']->id, $shiftMorn->id],
            [$site1->id, $createdGuards['SEC-G-005']->id, $shiftNigh->id],
            [$site2->id, $createdGuards['SEC-G-002']->id, $shiftMorn->id],
            [$site2->id, $createdGuards['SEC-G-006']->id, $shiftEven->id],
            [$site3->id, $createdGuards['SEC-G-003']->id, $shiftGen->id],
            [$site3->id, $createdGuards['SEC-G-007']->id, $shiftNigh->id],
            [$site4->id, $createdGuards['SEC-G-004']->id, $shiftMorn->id],
            [$site4->id, $createdGuards['SEC-G-008']->id, $shiftEven->id],
        ];

        foreach ($rosterPairs as $pair) {
            DutyRoster::create([
                'site_id' => $pair[0],
                'guard_id' => $pair[1],
                'shift_id' => $pair[2],
                'roster_date' => $today->toDateString(),
                'status' => 'completed',
            ]);
        }

        // 9. Attendance
        // Create past 7 days attendance for all active deployed guards
        for ($d = 6; $d >= 0; $d--) {
            $date = $today->copy()->subDays($d);
            foreach ($rosterPairs as $pair) {
                $status = 'present';
                $overtime = 0.00;
                $late = 0;

                // Simulate realistic variety
                if ($d === 1 && $pair[1] === $createdGuards['SEC-G-005']->id) {
                    $status = 'overtime';
                    $overtime = 2.00;
                } elseif ($d === 2 && $pair[1] === $createdGuards['SEC-G-008']->id) {
                    $late = 15;
                }

                $att = Attendance::create([
                    'guard_id' => $pair[1],
                    'site_id' => $pair[0],
                    'shift_id' => $pair[2],
                    'attendance_date' => $date->toDateString(),
                    'status' => $status,
                    'check_in_time' => '05:55:00',
                    'check_out_time' => '14:05:00',
                    'late_minutes' => $late,
                    'overtime_hours' => $overtime,
                    'verified_by_id' => $supervisor->id,
                    'remarks' => $d === 0 ? 'Today verified on duty' : 'Regular duty completed',
                    'latitude' => 26.1445169,
                    'longitude' => 91.7362365,
                ]);

                if ($late > 0) {
                    AttendanceAudit::create([
                        'attendance_id' => $att->id,
                        'changed_by_id' => $supervisor->id,
                        'old_status' => 'absent',
                        'new_status' => 'present',
                        'reason' => 'Guard arrived 15 mins late due to public transport delay and was permitted with late mark.',
                        'created_at' => now(),
                    ]);
                }
            }
        }

        // Add 1 guard on approved leave today
        Attendance::create([
            'guard_id' => $createdGuards['SEC-G-009']->id,
            'attendance_date' => $today->toDateString(),
            'status' => 'leave',
            'remarks' => 'Approved casual leave',
        ]);

        // Add 1 absent guard
        Attendance::create([
            'guard_id' => $createdGuards['SEC-G-010']->id,
            'attendance_date' => $today->toDateString(),
            'status' => 'absent',
            'remarks' => 'Uninformed absence',
        ]);

        // 10. Leaves
        Leave::create([
            'guard_id' => $createdGuards['SEC-G-009']->id,
            'leave_type' => 'casual',
            'start_date' => $today->copy()->subDay()->toDateString(),
            'end_date' => $today->copy()->addDay()->toDateString(),
            'days_count' => 3,
            'reason' => 'Family ceremony in village',
            'status' => 'approved',
            'approved_by_id' => $hr->id,
        ]);

        Leave::create([
            'guard_id' => $createdGuards['SEC-G-002']->id,
            'leave_type' => 'sick',
            'start_date' => $today->copy()->addDays(5)->toDateString(),
            'end_date' => $today->copy()->addDays(7)->toDateString(),
            'days_count' => 3,
            'reason' => 'Scheduled eye medical checkup and rest',
            'status' => 'pending',
        ]);

        // 11. Salary Advances
        SalaryAdvance::create([
            'guard_id' => $createdGuards['SEC-G-001']->id,
            'advance_code' => 'ADV-2026-001',
            'amount' => 5000.00,
            'request_date' => $today->copy()->subDays(20)->toDateString(),
            'status' => 'approved',
            'approved_by_id' => $admin->id,
            'recovered_amount' => 2500.00,
            'reason' => 'Children school admission fee',
        ]);

        SalaryAdvance::create([
            'guard_id' => $createdGuards['SEC-G-006']->id,
            'advance_code' => 'ADV-2026-002',
            'amount' => 3000.00,
            'request_date' => $today->copy()->subDays(5)->toDateString(),
            'status' => 'pending',
            'recovered_amount' => 0.00,
            'reason' => 'Emergency motorcycle repair',
        ]);

        // 12. Payroll for previous month (October 2026)
        $prevMonth = 10;
        $prevYear = 2026;
        $activeGuardsList = [
            $createdGuards['SEC-G-001'],
            $createdGuards['SEC-G-002'],
            $createdGuards['SEC-G-003'],
            $createdGuards['SEC-G-004'],
            $createdGuards['SEC-G-005'],
            $createdGuards['SEC-G-006'],
            $createdGuards['SEC-G-007'],
            $createdGuards['SEC-G-008'],
        ];

        foreach ($activeGuardsList as $idx => $g) {
            $workingDays = 31;
            $present = 30.0;
            $overtimeHrs = ($idx === 0) ? 6.00 : 0.00;
            $overtimeRate = $g->overtime_rate;
            $overtimeAmt = $overtimeHrs * $overtimeRate;
            $advanceDed = ($g->guard_code === 'SEC-G-001') ? 2500.00 : 0.00;
            $gross = $g->basic_salary + $g->allowances + $overtimeAmt;
            $net = $gross - $advanceDed;

            Payroll::create([
                'payroll_code' => sprintf('PAY-%04d%02d-%03d', $prevYear, $prevMonth, $idx + 1),
                'guard_id' => $g->id,
                'month' => $prevMonth,
                'year' => $prevYear,
                'salary_type' => 'monthly',
                'basic_salary' => $g->basic_salary,
                'allowances' => $g->allowances,
                'overtime_hours' => $overtimeHrs,
                'overtime_rate' => $overtimeRate,
                'overtime_amount' => $overtimeAmt,
                'total_working_days' => $workingDays,
                'present_days' => $present,
                'leave_deductions' => 0.00,
                'advance_deductions' => $advanceDed,
                'other_deductions' => 0.00,
                'gross_salary' => $gross,
                'net_salary' => $net,
                'status' => 'paid',
                'payment_date' => Carbon::parse('2026-11-05'),
                'payment_mode' => 'bank_transfer',
                'payment_reference' => 'NEFT' . rand(10000000, 99999999),
                'notes' => 'Disbursed via bank transfer on 5th Nov',
                'generated_by_id' => $accountant->id,
            ]);
        }

        // Create pending payroll draft for current month (November 2026) for G-001 & G-002
        Payroll::create([
            'payroll_code' => 'PAY-202611-001',
            'guard_id' => $createdGuards['SEC-G-001']->id,
            'month' => 11,
            'year' => 2026,
            'salary_type' => 'monthly',
            'basic_salary' => 19500.00,
            'allowances' => 2500.00,
            'overtime_hours' => 4.00,
            'overtime_rate' => 120.00,
            'overtime_amount' => 480.00,
            'total_working_days' => 30,
            'present_days' => 24.0,
            'leave_deductions' => 0.00,
            'advance_deductions' => 2500.00,
            'other_deductions' => 0.00,
            'gross_salary' => 22480.00,
            'net_salary' => 19980.00,
            'status' => 'pending',
            'notes' => 'Calculation based on attendance till 24th Nov',
            'generated_by_id' => $accountant->id,
        ]);

        // 13. Invoices & Billing
        // Invoice 1: Tech Park - Paid
        $inv1 = Invoice::create([
            'invoice_number' => 'GSS-INV-2026-101',
            'client_id' => $client1->id,
            'site_id' => $site1->id,
            'billing_period' => 'October 2026',
            'issue_date' => Carbon::parse('2026-11-01'),
            'due_date' => Carbon::parse('2026-11-30'),
            'guard_count' => 3,
            'rate_per_guard' => 26000.00,
            'subtotal' => 78000.00,
            'additional_charges' => 2000.00,
            'additional_charges_desc' => 'Baggage scanner operator premium',
            'discount_amount' => 0.00,
            'gst_rate' => 18.00,
            'cgst_amount' => 7200.00,
            'sgst_amount' => 7200.00,
            'igst_amount' => 0.00,
            'total_amount' => 94400.00,
            'paid_amount' => 94400.00,
            'balance_amount' => 0.00,
            'status' => 'paid',
            'notes' => 'Payment received via RTGS on 10th Nov.',
        ]);

        InvoicePayment::create([
            'invoice_id' => $inv1->id,
            'payment_receipt_number' => 'REC-2026-001',
            'payment_date' => Carbon::parse('2026-11-10'),
            'amount' => 94400.00,
            'payment_mode' => 'bank_transfer',
            'transaction_reference' => 'HDFC-RTGS-9812401',
            'notes' => 'Full payment against GSS-INV-2026-101',
            'recorded_by_id' => $accountant->id,
        ]);

        // Invoice 2: Brahmaputra Heights - Partially Paid
        $inv2 = Invoice::create([
            'invoice_number' => 'GSS-INV-2026-102',
            'client_id' => $client2->id,
            'site_id' => $site2->id,
            'billing_period' => 'October 2026',
            'issue_date' => Carbon::parse('2026-11-01'),
            'due_date' => Carbon::parse('2026-11-15'),
            'guard_count' => 2,
            'rate_per_guard' => 24000.00,
            'subtotal' => 48000.00,
            'additional_charges' => 0.00,
            'discount_amount' => 1000.00,
            'gst_rate' => 18.00,
            'cgst_amount' => 4230.00,
            'sgst_amount' => 4230.00,
            'igst_amount' => 0.00,
            'total_amount' => 55460.00,
            'paid_amount' => 30000.00,
            'balance_amount' => 25460.00,
            'status' => 'partially_paid',
            'notes' => 'Part payment of Rs. 30,000 received. Balance cheque expected by end of month.',
        ]);

        InvoicePayment::create([
            'invoice_id' => $inv2->id,
            'payment_receipt_number' => 'REC-2026-002',
            'payment_date' => Carbon::parse('2026-11-12'),
            'amount' => 30000.00,
            'payment_mode' => 'cheque',
            'transaction_reference' => 'CHQ-890112-SBI',
            'notes' => 'Part payment cheque cleared',
            'recorded_by_id' => $accountant->id,
        ]);

        // Invoice 3: Kamakhya Mega Warehousing - Sent (Outstanding)
        Invoice::create([
            'invoice_number' => 'GSS-INV-2026-103',
            'client_id' => $client3->id,
            'site_id' => $site3->id,
            'billing_period' => 'October 2026',
            'issue_date' => Carbon::parse('2026-11-05'),
            'due_date' => Carbon::parse('2026-12-05'),
            'guard_count' => 3,
            'rate_per_guard' => 27000.00,
            'subtotal' => 81000.00,
            'additional_charges' => 3000.00,
            'additional_charges_desc' => 'Armed guard ammunition & patrol charge',
            'discount_amount' => 0.00,
            'gst_rate' => 18.00,
            'cgst_amount' => 7560.00,
            'sgst_amount' => 7560.00,
            'igst_amount' => 0.00,
            'total_amount' => 99120.00,
            'paid_amount' => 0.00,
            'balance_amount' => 99120.00,
            'status' => 'sent',
            'notes' => 'Invoice dispatched. Due on 5th Dec.',
        ]);

        // Invoice 4: Silver Square Mall - Overdue
        Invoice::create([
            'invoice_number' => 'GSS-INV-2026-094',
            'client_id' => $client4->id,
            'site_id' => $site4->id,
            'billing_period' => 'September 2026',
            'issue_date' => Carbon::parse('2026-10-01'),
            'due_date' => Carbon::parse('2026-10-25'),
            'guard_count' => 4,
            'rate_per_guard' => 25000.00,
            'subtotal' => 100000.00,
            'additional_charges' => 0.00,
            'discount_amount' => 0.00,
            'gst_rate' => 18.00,
            'cgst_amount' => 9000.00,
            'sgst_amount' => 9000.00,
            'igst_amount' => 0.00,
            'total_amount' => 118000.00,
            'paid_amount' => 0.00,
            'balance_amount' => 118000.00,
            'status' => 'overdue',
            'notes' => 'Payment overdue by 30 days. Reminder notice sent to management.',
        ]);

        // 14. Equipment & Uniform Inventory
        $itemsData = [
            ['item_code' => 'EQP-UNI-001', 'name' => 'Agency Uniform Set (Shirt + Trouser + Beret)', 'category' => 'uniform', 'total_quantity' => 50, 'available_quantity' => 28, 'unit_cost' => 1250.00],
            ['item_code' => 'EQP-SHO-001', 'name' => 'Steel-Toe Tactical Security Boots', 'category' => 'shoes', 'total_quantity' => 40, 'available_quantity' => 20, 'unit_cost' => 950.00],
            ['item_code' => 'EQP-TOR-001', 'name' => 'High-Power Rechargeable LED Search Torch', 'category' => 'torch', 'total_quantity' => 25, 'available_quantity' => 14, 'unit_cost' => 650.00],
            ['item_code' => 'EQP-BAT-001', 'name' => 'Polycarbonate Fibre Lathi / Baton (1 meter)', 'category' => 'baton', 'total_quantity' => 35, 'available_quantity' => 22, 'unit_cost' => 320.00],
            ['item_code' => 'EQP-WKI-001', 'name' => 'Motorola Two-Way VHF Walkie-Talkie Set', 'category' => 'communication', 'total_quantity' => 15, 'available_quantity' => 7, 'unit_cost' => 3800.00],
            ['item_code' => 'EQP-RNC-001', 'name' => 'Heavy-Duty Fluorescent Raincoat', 'category' => 'raincoat', 'total_quantity' => 30, 'available_quantity' => 18, 'unit_cost' => 580.00],
            ['item_code' => 'EQP-SAF-001', 'name' => 'Reflective Night Patrol Safety Jacket', 'category' => 'safety_gear', 'total_quantity' => 40, 'available_quantity' => 24, 'unit_cost' => 280.00],
            ['item_code' => 'EQP-IDC-001', 'name' => 'RFID Lanyard ID Badge Card', 'category' => 'id_card', 'total_quantity' => 100, 'available_quantity' => 65, 'unit_cost' => 85.00],
        ];

        $createdEquipment = [];
        foreach ($itemsData as $eq) {
            $createdEquipment[$eq['item_code']] = EquipmentItem::create($eq);
        }

        // Issue equipment to guards
        EquipmentAssignment::create([
            'equipment_item_id' => $createdEquipment['EQP-UNI-001']->id,
            'guard_id' => $createdGuards['SEC-G-001']->id,
            'quantity' => 2,
            'issue_date' => Carbon::parse('2026-06-01'),
            'condition_on_issue' => 'Brand New',
            'status' => 'issued',
            'notes' => 'Issued 2 sets of uniform with cap and shoulder badges',
        ]);

        EquipmentAssignment::create([
            'equipment_item_id' => $createdEquipment['EQP-TOR-001']->id,
            'guard_id' => $createdGuards['SEC-G-005']->id,
            'quantity' => 1,
            'issue_date' => Carbon::parse('2026-07-01'),
            'condition_on_issue' => 'Good',
            'status' => 'issued',
            'notes' => 'For night surveillance at Tech Park',
        ]);

        EquipmentAssignment::create([
            'equipment_item_id' => $createdEquipment['EQP-WKI-001']->id,
            'guard_id' => $createdGuards['SEC-G-003']->id,
            'quantity' => 1,
            'issue_date' => Carbon::parse('2026-08-01'),
            'condition_on_issue' => 'New',
            'status' => 'issued',
            'notes' => 'Walkie-Talkie Channel 4 assigned',
        ]);

        // 15. Incidents
        Incident::create([
            'incident_code' => 'INC-2026-001',
            'client_id' => $client4->id,
            'site_id' => $site4->id,
            'incident_datetime' => Carbon::parse('2026-11-18 17:35:00'),
            'reported_by' => 'Rina Begum (Lady Guard)',
            'category' => 'theft',
            'description' => 'A shopper left a handbag containing cash and mobile at 2nd floor seating area. Recovered by guard on duty and secured in mall control room.',
            'action_taken' => 'Verified customer ID and handed over bag intact in presence of Mall Admin Neha Agarwal.',
            'involved_guards' => 'SEC-G-004 (Rina Begum)',
            'resolution_status' => 'resolved',
            'confidential' => false,
            'resolved_at' => Carbon::parse('2026-11-18 19:15:00'),
        ]);

        Incident::create([
            'incident_code' => 'INC-2026-002',
            'client_id' => $client2->id,
            'site_id' => $site2->id,
            'incident_datetime' => Carbon::parse('2026-11-20 21:10:00'),
            'reported_by' => 'Dhrubajyoti Deka',
            'category' => 'visitor_dispute',
            'description' => 'An aggressive visitor refused to provide mobile number and car registration at the society boom barrier and argued with gate guard.',
            'action_taken' => 'Supervisor Ramesh Bordoloi reached gate, contacted resident Flat 402, verified guest and pacified situation.',
            'involved_guards' => 'SEC-G-002 (Dhrubajyoti Deka)',
            'resolution_status' => 'closed',
            'confidential' => false,
            'resolved_at' => Carbon::parse('2026-11-20 21:40:00'),
        ]);

        Incident::create([
            'incident_code' => 'INC-2026-003',
            'client_id' => $client3->id,
            'site_id' => $site3->id,
            'incident_datetime' => Carbon::parse('2026-11-23 02:40:00'),
            'reported_by' => 'Tapan Hazarika',
            'category' => 'unauthorized_entry',
            'description' => 'Two unidentified individuals attempted to cut perimeter fence wire near north-west corner of logistics warehouse yard.',
            'action_taken' => 'Guard Tapan flashed searchlight, raised siren alarm and alerted armed guard Manoj Rabha. Intruders fled into adjoining fields. Police Changsari PS notified.',
            'involved_guards' => 'SEC-G-007 (Tapan Hazarika), SEC-G-003 (Manoj Rabha)',
            'resolution_status' => 'investigating',
            'confidential' => true,
        ]);

        // 16. Expenses
        Expense::create([
            'expense_code' => 'EXP-2026-081',
            'expense_date' => Carbon::parse('2026-11-04'),
            'category' => 'uniform_expenses',
            'amount' => 18500.00,
            'payment_mode' => 'bank_transfer',
            'payee' => 'National Garments & Uniform Makers Guwahati',
            'reference_number' => 'UPI/98124401',
            'description' => 'Procurement of 15 sets of winter jackets and dark blue trousers for guards',
            'created_by_id' => $accountant->id,
        ]);

        Expense::create([
            'expense_code' => 'EXP-2026-082',
            'expense_date' => Carbon::parse('2026-11-08'),
            'category' => 'equipment_expenses',
            'amount' => 6400.00,
            'payment_mode' => 'cash',
            'payee' => 'Pioneer Electronics Panbazar',
            'reference_number' => 'CASH-REC-1102',
            'description' => 'Purchase of rechargeable lithium batteries and chargers for night torches',
            'created_by_id' => $accountant->id,
        ]);

        Expense::create([
            'expense_code' => 'EXP-2026-083',
            'expense_date' => Carbon::parse('2026-11-15'),
            'category' => 'transportation',
            'amount' => 4500.00,
            'payment_mode' => 'upi',
            'payee' => 'Indian Oil Corporation Petrol Pump Ulubari',
            'reference_number' => 'UPI/12093488',
            'description' => 'Fuel reimbursement for site supervisor motorcycle inspection rounds',
            'created_by_id' => $operations->id,
        ]);

        Expense::create([
            'expense_code' => 'EXP-2026-084',
            'expense_date' => Carbon::parse('2026-11-01'),
            'category' => 'office_rent',
            'amount' => 35000.00,
            'payment_mode' => 'bank_transfer',
            'payee' => 'Pranab Kumar Goswami (Landlord)',
            'reference_number' => 'NEFT-RENT-NOV26',
            'description' => 'Agency central office rent for Christian Basti premises for Nov 2026',
            'created_by_id' => $accountant->id,
        ]);

        Expense::create([
            'expense_code' => 'EXP-2026-085',
            'expense_date' => Carbon::parse('2026-11-12'),
            'category' => 'electricity',
            'amount' => 5200.00,
            'payment_mode' => 'bank_transfer',
            'payee' => 'APDCL (Assam Power Distribution Company Ltd.)',
            'reference_number' => 'APDCL-CONS-99120',
            'description' => 'Electricity bill payment for head office',
            'created_by_id' => $accountant->id,
        ]);

        // 17. Audit Logs
        AuditLog::log('system_initialized', 'system', null, 'System database seeded with demo data and business settings.');
        AuditLog::log('created', 'clients', $client1->id, 'Created client Brahmaputra Tech Park Ltd.');
        AuditLog::log('created', 'deployments', $dep1->id, 'Deployed guard Bhaben Chandra Nath to Tech Park Tower A.');
    }
}

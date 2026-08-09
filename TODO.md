# TODO - توحيد وترابط منصة إيدو لينك (SchoolPla)

## الأهداف
ربط جميع موديلات المنصة في كتلة واحدة مترابطة، توحيد الـ Seeders، وإصلاح التناقضات في العلاقات.

## الخطوات

### المرحلة 1: توحيد الـ Seeders
- [x] تفعيل استدعاء جميع الـ Seeders من `DatabaseSeeder.php`
- [x] تأكيد عمل `php artisan db:seed` (يستدعي AccountsSeeder + FeatureDataSeeder + ExtraDataSeeder)
- [x] إضافة `school_id` للمستخدمين المعلّقين في ExtraDataSeeder
- [ ] تنظيف الملفات الخارجية المكررة (seed_accounts.php, seed_feature_data.php, seed_extra.php, run_seeder.php, test_seed_parts.php)

### المرحلة 2: إصلاح تناقضات المخطط (Schema)
- [x] **schedules**: توحيد قيم أيام الأسبوع (الـ migration يستخدم saturday→wednesday وهو مطابق للـ Model)
- [x] **announcements**: توحيد حقل `target` (تم تعديل الـ Model لاستخدام `target`)
- [x] **guardians**: توحيد حقل `relation` / `relationship` (الـ Model يدعم الاثنين للتوافق)
- [x] **QuizQuestion**: إضافة علاقة `questionBank()`

### المرحلة 3: إكمال العلاقات الناقصة بين الموديلات
- [x] **School**: إضافة علاقات للجداول الجديدة (fees, books, buses, quizzes, expenses, incomes)
- [x] **Book**: علاقة `school()` عبر trait `UsesSchoolSchema`
- [x] **Bus**: علاقة `school()` عبر trait `UsesSchoolSchema`
- [x] **Fee/StudentFee/Payment/Expense/Income**: علاقة `school()` عبر trait
- [x] **OnlineQuiz/QuestionBank/QuizAttempt/ClassroomMaterial**: علاقة `school()` عبر trait
- [x] **TransportRoute**: علاقة `school()` عبر trait + إضافة علاقات
- [x] **Subject**: إضافة علاقة `grades()` (belongsToMany) + quizzes + questionBanks + materials
- [x] **User**: العلاقات الأساسية (role, teacher, guardian, messages, notifications)

### المرحلة 4: ربط الأدوار والعمليات
- [ ] **Super Admin**: إدارة كل المدارس + نظرة عامة على الميزات
- [ ] **Admin**: إدارة كاملة لمدرسته (الموارد البشرية + الأكاديمي + المالية + المكتبة + النقل)
- [ ] **Teacher**: الحضور/الدرجات/الواجبات/السلوك/بنك الأسئلة/الاختبارات/المواد
- [ ] **Parent**: متابعة الأبناء (الدرجات/الحضور/الرسوم/النقل/الاختبارات)

### المرحلة 5: الاختبار والتحقق
- [ ] تشغيل `php artisan db:seed` والتأكد من نجاحه
- [ ] التحقق من ترابط العلاقات
- [ ] اختبار وصول كل دور لصلاحياته فقط

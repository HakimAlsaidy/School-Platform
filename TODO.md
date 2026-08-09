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
- [ ] **schedules**: توحيد قيم أيام الأسبوع بين الـ migration والـ Model
- [ ] **announcements**: توحيد حقل `target` / `target_audience`
- [ ] **guardians**: توحيد حقل `relation` / `relationship`
- [ ] **QuizQuestion**: إضافة علاقات صحيحة مع المدرسة

### المرحلة 3: إكمال العلاقات الناقصة بين الموديلات
- [ ] **School**: إضافة علاقات للجداول الجديدة (fees, books, buses, quizzes, expenses, incomes)
- [ ] **Book**: إضافة علاقة `school()`
- [ ] **Bus**: إضافة علاقة `school()`
- [ ] **Fee/StudentFee/Payment/Expense/Income**: تأكيد علاقة `school()`
- [ ] **OnlineQuiz/QuestionBank/QuizAttempt/ClassroomMaterial**: إضافة علاقة `school()` وعلاقات عكسية
- [ ] **TransportRoute**: إضافة علاقة `school()` وترتيب التنسيق
- [ ] **Subject**: إضافة علاقة `grades()` (belongsToMany)
- [ ] **User**: إضافة علاقات إضافية حسب الحاجة

### المرحلة 4: ربط الأدوار والعمليات
- [ ] **Super Admin**: إدارة كل المدارس + نظرة عامة على الميزات
- [ ] **Admin**: إدارة كاملة لمدرسته (الموارد البشرية + الأكاديمي + المالية + المكتبة + النقل)
- [ ] **Teacher**: الحضور/الدرجات/الواجبات/السلوك/بنك الأسئلة/الاختبارات/المواد
- [ ] **Parent**: متابعة الأبناء (الدرجات/الحضور/الرسوم/النقل/الاختبارات)

### المرحلة 5: الاختبار والتحقق
- [ ] تشغيل `php artisan db:seed` والتأكد من نجاحه
- [ ] التحقق من ترابط العلاقات
- [ ] اختبار وصول كل دور لصلاحياته فقط

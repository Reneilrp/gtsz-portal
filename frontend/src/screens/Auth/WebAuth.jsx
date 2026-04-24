import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import {
    ChevronLeft,
    AlertCircle,
    Mail,
    Lock,
    Eye,
    EyeOff,
    User,
    Phone,
    MapPin,
    Calendar,
    Briefcase,
    GraduationCap,
    Loader2,
    Check,
} from "lucide-react";
import Logo from "../../assets/Logo.png";
import api from "../../utils/api";
import toast, { Toaster } from "react-hot-toast";

function AuthScreen({ isRegister = false, onLogin = () => { } }) {
    const navigate = useNavigate();
    const [isLogin, setIsLogin] = useState(!isRegister);

    useEffect(() => {
        setIsLogin(!isRegister);
    }, [isRegister]);

    const [showPassword, setShowPassword] = useState(false);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState("");
    const [showConfirmPassword, setShowConfirmPassword] = useState(false);
    const [fieldErrors, setFieldErrors] = useState({});
    const [schoolYears, setSchoolYears] = useState([]);

    const [formData, setFormData] = useState({
        role: "Student", // Default role
        first_name: "",
        last_name: "",
        email: "",
        password: "",
        password_confirmation: "",
        // Student specific
        student_number: "",
        birth_date: "",
        gender: "",
        address: "",
        guardian_name: "",
        guardian_contact: "",
        school_year_id: "",
        // Teacher specific
        employee_number: "",
        department: "",
        hired_date: "",
    });

    useEffect(() => {
        if (!isLogin) {
            // Fetch school years for student registration
            api.get("/school-years").then((res) => {
                setSchoolYears(res.data);
                const active = res.data.find(sy => sy.is_active);
                if (active) {
                    setFormData(prev => ({ ...prev, school_year_id: active.id }));
                } else if (res.data.length > 0) {
                    setFormData(prev => ({ ...prev, school_year_id: res.data[0].id }));
                }
            }).catch(() => { });
        }
    }, [isLogin]);

    const handleInputChange = (field, value) => {
        setFormData((prev) => ({ ...prev, [field]: value }));
        setError("");
        setFieldErrors((prev) => ({ ...prev, [field]: "" }));
    };

    const handleLogin = async (e) => {
        e?.preventDefault();
        setLoading(true);
        setError("");

        try {
            const result = await api.post("/login", {
                email: formData.email,
                password: formData.password,
            });

            const data = result.data;
            localStorage.setItem("authToken", data.token);
            localStorage.setItem("userData", JSON.stringify(data.user));
            api.defaults.headers.common["Authorization"] = `Bearer ${data.token}`;
            
            onLogin(data.user);
            toast.success("Welcome back!");
            navigate("/"); // Adjust based on role if needed
        } catch (err) {
            setError(err.response?.data?.message || "Login failed. Please check your credentials.");
        } finally {
            setLoading(false);
        }
    };

    const handleRegister = async (e) => {
        e?.preventDefault();
        setLoading(true);
        setError("");
        setFieldErrors({});

        try {
            const result = await api.post("/register", formData);
            const data = result.data;

            localStorage.setItem("authToken", data.token);
            localStorage.setItem("userData", JSON.stringify(data.user));
            api.defaults.headers.common["Authorization"] = `Bearer ${data.token}`;

            toast.success("Registration successful!");
            onLogin(data.user);
            navigate("/");
        } catch (err) {
            if (err.response?.status === 422) {
                setFieldErrors(err.response.data.errors);
                setError("Please correct the errors below.");
            } else {
                setError(err.response?.data?.message || "Registration failed.");
            }
        } finally {
            setLoading(false);
        }
    };

    const inputClasses =
        "w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white rounded-xl focus:ring-2 focus:ring-blue-500 transition-all outline-none";
    const labelClasses = "block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5";
    const iconClasses = "w-5 h-5 text-gray-400 dark:text-gray-500";

    return (
        <div className="min-h-screen flex items-center justify-center py-12 px-4 bg-gray-50 dark:bg-gray-900">
            <Toaster />
            <div className="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 w-full max-w-2xl border border-gray-100 dark:border-gray-700 relative">
                
                {/* Header */}
                <div className="flex flex-col items-center mb-8">
                    <img src={Logo} alt="Logo" className="h-16 w-auto mb-4" />
                    <h2 className="text-3xl font-bold text-gray-900 dark:text-white">
                        {isLogin ? "Welcome Back" : "Join GTSZ Portal"}
                    </h2>
                    <p className="text-gray-500 dark:text-gray-400 mt-2">
                        {isLogin ? "Sign in to access your portal" : "Create an account to get started"}
                    </p>
                </div>

                {error && (
                    <div className="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl flex items-center gap-3 text-red-600 dark:text-red-400">
                        <AlertCircle className="w-5 h-5" />
                        <span className="text-sm font-medium">{error}</span>
                    </div>
                )}

                {isLogin ? (
                    <form onSubmit={handleLogin} className="space-y-6 max-w-md mx-auto">
                        <div>
                            <label className={labelClasses}>Email Address</label>
                            <div className="relative">
                                <Mail className={`absolute left-3 top-1/2 -translate-y-1/2 ${iconClasses}`} />
                                <input
                                    type="email"
                                    value={formData.email}
                                    onChange={(e) => handleInputChange("email", e.target.value)}
                                    className={inputClasses}
                                    placeholder="name@school.com"
                                    required
                                />
                            </div>
                        </div>

                        <div>
                            <label className={labelClasses}>Password</label>
                            <div className="relative">
                                <Lock className={`absolute left-3 top-1/2 -translate-y-1/2 ${iconClasses}`} />
                                <input
                                    type={showPassword ? "text" : "password"}
                                    value={formData.password}
                                    onChange={(e) => handleInputChange("password", e.target.value)}
                                    className={inputClasses}
                                    placeholder="••••••••"
                                    required
                                />
                                <button
                                    type="button"
                                    onClick={() => setShowPassword(!showPassword)}
                                    className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    {showPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                </button>
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-all flex items-center justify-center gap-2"
                        >
                            {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : "Sign In"}
                        </button>
                    </form>
                ) : (
                    <form onSubmit={handleRegister} className="space-y-6">
                        {/* Role Selector */}
                        <div className="flex bg-gray-100 dark:bg-gray-700 p-1 rounded-xl mb-6">
                            <button
                                type="button"
                                onClick={() => handleInputChange("role", "Student")}
                                className={`flex-1 flex items-center justify-center gap-2 py-2 rounded-lg text-sm font-bold transition-all ${formData.role === "Student" ? "bg-white dark:bg-gray-600 shadow-sm text-blue-600 dark:text-blue-400" : "text-gray-500"}`}
                            >
                                <GraduationCap className="w-4 h-4" /> Student
                            </button>
                            <button
                                type="button"
                                onClick={() => handleInputChange("role", "Teacher")}
                                className={`flex-1 flex items-center justify-center gap-2 py-2 rounded-lg text-sm font-bold transition-all ${formData.role === "Teacher" ? "bg-white dark:bg-gray-600 shadow-sm text-blue-600 dark:text-blue-400" : "text-gray-500"}`}
                            >
                                <Briefcase className="w-4 h-4" /> Teacher
                            </button>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* Common Fields */}
                            <div>
                                <label className={labelClasses}>First Name</label>
                                <div className="relative">
                                    <User className={`absolute left-3 top-1/2 -translate-y-1/2 ${iconClasses}`} />
                                    <input
                                        type="text"
                                        value={formData.first_name}
                                        onChange={(e) => handleInputChange("first_name", e.target.value)}
                                        className={`${inputClasses} ${fieldErrors.first_name ? 'border-red-500' : ''}`}
                                        required
                                    />
                                </div>
                                {fieldErrors.first_name && <p className="text-xs text-red-500 mt-1">{fieldErrors.first_name[0]}</p>}
                            </div>

                            <div>
                                <label className={labelClasses}>Last Name</label>
                                <div className="relative">
                                    <User className={`absolute left-3 top-1/2 -translate-y-1/2 ${iconClasses}`} />
                                    <input
                                        type="text"
                                        value={formData.last_name}
                                        onChange={(e) => handleInputChange("last_name", e.target.value)}
                                        className={`${inputClasses} ${fieldErrors.last_name ? 'border-red-500' : ''}`}
                                        required
                                    />
                                </div>
                                {fieldErrors.last_name && <p className="text-xs text-red-500 mt-1">{fieldErrors.last_name[0]}</p>}
                            </div>

                            <div className="md:col-span-2">
                                <label className={labelClasses}>Email Address</label>
                                <div className="relative">
                                    <Mail className={`absolute left-3 top-1/2 -translate-y-1/2 ${iconClasses}`} />
                                    <input
                                        type="email"
                                        value={formData.email}
                                        onChange={(e) => handleInputChange("email", e.target.value)}
                                        className={`${inputClasses} ${fieldErrors.email ? 'border-red-500' : ''}`}
                                        required
                                    />
                                </div>
                                {fieldErrors.email && <p className="text-xs text-red-500 mt-1">{fieldErrors.email[0]}</p>}
                            </div>

                            {/* Role Specific Fields */}
                            {formData.role === "Student" ? (
                                <>
                                    <div>
                                        <label className={labelClasses}>Student Number</label>
                                        <input
                                            type="text"
                                            value={formData.student_number}
                                            onChange={(e) => handleInputChange("student_number", e.target.value)}
                                            className={inputClasses}
                                            placeholder="2024-0001"
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClasses}>Birth Date</label>
                                        <input
                                            type="date"
                                            value={formData.birth_date}
                                            onChange={(e) => handleInputChange("birth_date", e.target.value)}
                                            className={inputClasses}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClasses}>School Year</label>
                                        <select
                                            value={formData.school_year_id}
                                            onChange={(e) => handleInputChange("school_year_id", e.target.value)}
                                            className={inputClasses + " pl-4"}
                                            required
                                        >
                                            <option value="">Select School Year</option>
                                            {schoolYears.map(sy => (
                                                <option key={sy.id} value={sy.id}>{sy.label}</option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="md:col-span-2">
                                        <label className={labelClasses}>Home Address</label>
                                        <div className="relative">
                                            <MapPin className={`absolute left-3 top-3 ${iconClasses}`} />
                                            <textarea
                                                value={formData.address}
                                                onChange={(e) => handleInputChange("address", e.target.value)}
                                                className={`${inputClasses} pl-10 h-20 resize-none`}
                                                required
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label className={labelClasses}>Guardian Name</label>
                                        <input
                                            type="text"
                                            value={formData.guardian_name}
                                            onChange={(e) => handleInputChange("guardian_name", e.target.value)}
                                            className={inputClasses}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClasses}>Guardian Contact</label>
                                        <input
                                            type="text"
                                            value={formData.guardian_contact}
                                            onChange={(e) => handleInputChange("guardian_contact", e.target.value)}
                                            className={inputClasses}
                                            required
                                        />
                                    </div>
                                </>
                            ) : (
                                <>
                                    <div>
                                        <label className={labelClasses}>Employee Number</label>
                                        <input
                                            type="text"
                                            value={formData.employee_number}
                                            onChange={(e) => handleInputChange("employee_number", e.target.value)}
                                            className={inputClasses}
                                            required
                                        />
                                    </div>
                                    <div>
                                        <label className={labelClasses}>Department</label>
                                        <select
                                            value={formData.department}
                                            onChange={(e) => handleInputChange("department", e.target.value)}
                                            className={inputClasses + " pl-4"}
                                            required
                                        >
                                            <option value="">Select Dept</option>
                                            <option value="IT">Information Technology</option>
                                            <option value="CS">Computer Science</option>
                                            <option value="ENG">Engineering</option>
                                        </select>
                                    </div>
                                    <div className="md:col-span-2">
                                        <label className={labelClasses}>Hired Date</label>
                                        <input
                                            type="date"
                                            value={formData.hired_date}
                                            onChange={(e) => handleInputChange("hired_date", e.target.value)}
                                            className={inputClasses}
                                            required
                                        />
                                    </div>
                                </>
                            )}

                            {/* Password Section */}
                            <div>
                                <label className={labelClasses}>Password</label>
                                <div className="relative">
                                    <Lock className={`absolute left-3 top-1/2 -translate-y-1/2 ${iconClasses}`} />
                                    <input
                                        type={showPassword ? "text" : "password"}
                                        value={formData.password}
                                        onChange={(e) => handleInputChange("password", e.target.value)}
                                        className={inputClasses}
                                        required
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowConfirmPassword(!showConfirmPassword)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
                                    >
                                        {showConfirmPassword ? <EyeOff className="w-5 h-5" /> : <Eye className="w-5 h-5" />}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label className={labelClasses}>Confirm Password</label>
                                <div className="relative">
                                    <Lock className={`absolute left-3 top-1/2 -translate-y-1/2 ${iconClasses}`} />
                                    <input
                                        type={showConfirmPassword ? "text" : "password"}
                                        value={formData.password_confirmation}
                                        onChange={(e) => handleInputChange("password_confirmation", e.target.value)}
                                        className={inputClasses}
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <button
                            type="submit"
                            disabled={loading}
                            className="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl transition-all shadow-lg hover:shadow-blue-500/25 flex items-center justify-center gap-2 mt-4"
                        >
                            {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : "Create Account"}
                        </button>
                    </form>
                )}

                <div className="mt-8 text-center border-t border-gray-100 dark:border-gray-700 pt-6">
                    <p className="text-gray-600 dark:text-gray-400">
                        {isLogin ? "Don't have an account? " : "Already have an account? "}
                        <button
                            onClick={() => setIsLogin(!isLogin)}
                            className="text-blue-600 dark:text-blue-400 font-bold hover:underline"
                        >
                            {isLogin ? "Sign Up" : "Sign In"}
                        </button>
                    </p>
                </div>
            </div>
        </div>
    );
}

export default AuthScreen;

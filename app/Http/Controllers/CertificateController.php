<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Services\Certificates\CertificateService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CertificateController extends Controller
{
    public function __construct(
        protected CertificateService $certificateService
    ) {}

    /**
     * Check course certificate completion eligibility for authenticated student.
     */
    public function eligibility(Course $course): View|JsonResponse
    {
        $user = auth()->user();
        $eligibility = $this->certificateService->checkEligibility($user, $course);
        $certificate = Certificate::where('user_id', $user->id)->where('course_id', $course->id)->first();

        if (request()->wantsJson()) {
            return response()->json($eligibility);
        }

        return view('certificates.eligibility', compact('course', 'eligibility', 'certificate'));
    }

    /**
     * Claim and issue certificate upon server-verified course completion.
     */
    public function claim(Request $request, Course $course): RedirectResponse
    {
        $user = auth()->user();

        $certificate = $this->certificateService->issueCertificate($user, $course);

        return redirect()->route('certificates.show', $certificate)
            ->with('success', 'Congratulations! Your official certificate of completion has been issued.');
    }

    /**
     * Display the full printable digital certificate document for the student.
     */
    public function show(Certificate $certificate): View
    {
        Gate::authorize('view', $certificate);

        $certificate->load(['course.category', 'course.instructors', 'user']);

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Public certificate verification page via cryptographic hash.
     *
     * Privacy Policy: Discloses only verified certificate data (name, course, date, grade, status).
     * Never exposes private student email, phone, physical address, or financial records.
     */
    public function verify(string $hash): View
    {
        $certificate = $this->certificateService->verifyByHash($hash);

        return view('certificates.verify', [
            'certificate' => $certificate,
            'lookupQuery' => $hash,
        ]);
    }

    /**
     * Public search lookup by certificate credential code (e.g. LB-CERT-2026-X9Y2).
     */
    public function search(Request $request): View
    {
        $code = (string) $request->query('code', '');
        $certificate = ! empty($code) ? $this->certificateService->verifyByCode($code) : null;

        return view('certificates.verify', [
            'certificate' => $certificate,
            'lookupQuery' => $code,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PreferenceController extends Controller
{
    protected array $allowedPreferences = [
        'Pintu Rumah',
        'Lemari & Kabinet',
        'Furniture',
        'Kantor & Bangunan Komersial',
        'Dekorasi Interior',
        'Renovasi & Proyek Bangunan',
    ];

    public function index(Request $request)
    {
        $selectedPreferences = $this->resolveSelectedPreferences($request);

        return view('preferences.onboarding', compact('selectedPreferences'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'preferences' => ['required', 'array', 'max:3'],
            'preferences.*' => ['string', Rule::in($this->allowedPreferences)],
        ]);

        $preferences = $validated['preferences'] ?? [];

        if (auth()->check()) {
            $this->syncUserPreferences(auth()->user(), $preferences);

            return redirect()->route('home')->with('success', 'Preferensi Anda berhasil diperbarui.');
        }

        if (empty($preferences)) {
            $request->session()->forget('guest_preferences');

            return redirect()->route('home');
        }

        $request->session()->put('guest_preferences', array_values(array_unique($preferences)));
        $request->session()->put('preference_modal_dismissed', true);

        return redirect()->route('home')->with('success', 'Preferensi Anda telah disimpan untuk rekomendasi awal.');
    }

    public function skip(Request $request)
    {
        $request->session()->forget('guest_preferences');
        $request->session()->put('preference_modal_dismissed', true);

        return redirect()->route('home');
    }

    public function profile()
    {
        $preferences = auth()->user()
            ->preferences()
            ->pluck('preference');

        return view('profile.preferences', compact('preferences'));
    }

    public function reset(Request $request)
    {
        $user = auth()->user();
        $user->preferences()->delete();
        $request->session()->forget('guest_preferences');
        $request->session()->flash('preference_reset_banner', true);

        return redirect()->route('home')->with('success', 'Preferensi berhasil direset.');
    }

    protected function resolveSelectedPreferences(Request $request): array
    {
        if (auth()->check()) {
            return auth()->user()->preferences()->pluck('preference')->all();
        }

        return $request->session()->get('guest_preferences', []);
    }

    protected function syncUserPreferences(User $user, array $preferences): void
    {
        $user->preferences()->delete();

        foreach (array_unique($preferences) as $preference) {
            $user->preferences()->create([
                'preference' => $preference,
            ]);
        }
    }
}

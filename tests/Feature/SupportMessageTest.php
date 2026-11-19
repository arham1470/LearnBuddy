<?php

namespace Tests\Feature;

use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupportMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_view_support_form(): void
    {
        $response = $this->get(route('support.create'));
        $response->assertStatus(200);
        $response->assertSee('How Can We');
        $response->assertSee('Help You?');
        $response->assertSee('support@learnbuddy.lk');
    }

    public function test_user_can_submit_support_ticket(): void
    {
        $payload = [
            'name' => 'Nimal Silva',
            'email' => 'nimal@example.com',
            'phone' => '+94771234567',
            'category' => 'account',
            'subject' => 'Cannot reset my account password',
            'message' => 'Hello support, I am unable to receive my password reset link. Please assist.',
        ];

        $response = $this->from(route('support.create'))->post(route('support.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('support_success');

        $this->assertDatabaseHas('support_messages', [
            'name' => 'Nimal Silva',
            'email' => 'nimal@example.com',
            'subject' => 'Cannot reset my account password',
            'status' => SupportMessage::STATUS_PENDING,
        ]);

        $followResponse = $this->followRedirects($response);
        $followResponse->assertSee('Message Sent Successfully!');
        $followResponse->assertSee('Ticket Submitted');
        $followResponse->assertSee('Return to Home');
    }

    public function test_authenticated_user_support_ticket_links_user_id(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'category' => 'billing',
            'subject' => 'Receipt verification inquiry',
            'message' => 'Please verify my recent bank payment receipt for Batch 02.',
        ];

        $response = $this->actingAs($user)->post(route('support.store'), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('support_messages', [
            'user_id' => $user->id,
            'email' => $user->email,
            'category' => 'billing',
            'status' => SupportMessage::STATUS_PENDING,
        ]);
    }

    public function test_ajax_support_submission_returns_json(): void
    {
        $payload = [
            'name' => 'Kamal Perera',
            'email' => 'kamal@example.com',
            'category' => 'general',
            'subject' => 'Physical classroom schedule inquiry',
            'message' => 'What are the upcoming batch dates for Colombo campus?',
        ];

        $response = $this->postJson(route('support.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $response->assertJsonStructure(['success', 'message', 'ticket_id']);
    }

    public function test_support_submission_validates_required_fields(): void
    {
        $response = $this->post(route('support.store'), []);
        $response->assertSessionHasErrors(['name', 'email', 'category', 'subject', 'message']);
    }

    public function test_non_admin_cannot_access_admin_support_desk(): void
    {
        $user = User::factory()->student()->create();

        $response = $this->actingAs($user)->get(route('admin.support.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_support_desk_and_messages(): void
    {
        $admin = User::factory()->admin()->create();

        $ticket = SupportMessage::create([
            'name' => 'Dilshan Fernando',
            'email' => 'dilshan@example.com',
            'subject' => 'Urgent Account Help Needed',
            'category' => 'account',
            'message' => 'My account says pending approval for 2 days.',
            'status' => SupportMessage::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.support.index'));
        $response->assertStatus(200);
        $response->assertSee('Support &amp; User Inquiries', false);
        $response->assertSee('Urgent Account Help Needed');
        $response->assertSee('Dilshan Fernando');
        $response->assertSee('dilshan@example.com');
        $response->assertSee('Reply via Gmail');
    }

    public function test_admin_reply_saves_reply_and_redirects_to_gmail_compose(): void
    {
        $admin = User::factory()->admin()->create();

        $ticket = SupportMessage::create([
            'name' => 'Saman Jayasuriya',
            'email' => 'saman@example.com',
            'subject' => 'Course access question',
            'category' => 'courses',
            'message' => 'I cannot see the recorded lecture for week 3.',
            'status' => SupportMessage::STATUS_PENDING,
        ]);

        $replyText = 'Hello Saman, we have restored access to week 3 lecture video. Please check your dashboard.';

        $response = $this->actingAs($admin)->post(route('admin.support.reply', $ticket->id), [
            'reply' => $replyText,
        ]);

        $ticket->refresh();
        $this->assertEquals(SupportMessage::STATUS_REPLIED, $ticket->status);
        $this->assertEquals($replyText, $ticket->admin_reply);
        $this->assertEquals($admin->id, $ticket->replied_by);
        $this->assertNotNull($ticket->replied_at);

        // Assert redirect to Gmail compose URL
        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('mail.google.com/mail', $targetUrl);
        $this->assertStringContainsString('view=cm', $targetUrl);
        $this->assertStringContainsString(rawurlencode('saman@example.com'), $targetUrl);
    }

    public function test_admin_can_toggle_support_ticket_status(): void
    {
        $admin = User::factory()->admin()->create();

        $ticket = SupportMessage::create([
            'name' => 'Praveen Silva',
            'email' => 'praveen@example.com',
            'subject' => 'Payment confirmation inquiry',
            'category' => 'billing',
            'message' => 'Has my payment been confirmed?',
            'status' => SupportMessage::STATUS_PENDING,
        ]);

        $this->actingAs($admin)->post(route('admin.support.toggle-status', $ticket->id));
        $ticket->refresh();
        $this->assertEquals(SupportMessage::STATUS_REPLIED, $ticket->status);

        $this->actingAs($admin)->post(route('admin.support.toggle-status', $ticket->id));
        $ticket->refresh();
        $this->assertEquals(SupportMessage::STATUS_PENDING, $ticket->status);
    }

    public function test_admin_can_delete_support_ticket(): void
    {
        $admin = User::factory()->admin()->create();

        $ticket = SupportMessage::create([
            'name' => 'Spam Bot',
            'email' => 'spam@bot.com',
            'subject' => 'Spam subject',
            'category' => 'general',
            'message' => 'Some spam message text here...',
            'status' => SupportMessage::STATUS_PENDING,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.support.destroy', $ticket->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('support_messages', [
            'id' => $ticket->id,
        ]);
    }
}

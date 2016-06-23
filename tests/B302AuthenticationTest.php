<?php

use PiekJ\B302Authentication\User;

class B302AuthenticationTest extends TestCase {

    private $user;
    private $unconfirmedUser;

    /**
     * Set up some default properties.
     */
    public function setUp()
    {
        parent::setUp();

        $this->prepareDatabaseForTests();

        Mail::pretend(true);

        $this->user = new User();
        $this->user->username = 'Admin';
        $this->user->email = 'admin@admin.nl';
        $this->user->password = 'test123';
        $this->user->password_confirmation = 'test123';
        $this->user->confirmation_code = md5(uniqid(mt_rand(), true));
        $this->user->confirmed = 1;

        $this->user->save();

        $this->unconfirmedUser = new User();
        $this->unconfirmedUser->username = 'Admin2';
        $this->unconfirmedUser->email = 'admin2@admin2.nl';
        $this->unconfirmedUser->password = 'test123';
        $this->unconfirmedUser->password_confirmation = 'test123';
        $this->unconfirmedUser->confirmation_code = md5(uniqid(mt_rand(), true));
        $this->unconfirmedUser->confirmed = 0;

        $this->unconfirmedUser->save();
    }

    private function prepareDatabaseForTests()
    {
        Artisan::call('migrate');
        Artisan::call('db:seed');
    }


    /**
     * Test the login form.
     */
    public function testLoginForm()
    {
        // Get login page.
        $crawler = $this->client->request('GET', '/users/login');

        $this->assertTrue($this->client->getResponse()->isOk());

        $this->assertEquals(1, $crawler->filter('html:contains("Login")')->count());


        // Try to login with wrong details.
        $form = $crawler->selectButton('Login')->form();

        $form['email'] = $this->user->email;
        $form['password'] = 'admin';

        $crawler = $this->client->submit($form);

        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('error');
        $this->assertHasOldInput();

        // Try to login with right details.
        $form['password'] = 'test123';

        $crawler = $this->client->submit($form);

        $this->assertRedirectedTo('/');

        Auth::logout();

        // Try to login with a unconfirmed user.
        $form['email'] = $this->unconfirmedUser->email;
        $form['password'] = 'test123';

        $crawler = $this->client->submit($form);

        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('error');
        $this->assertHasOldInput();
    }

    /**
     * Test the registration form.
     */
    public function testCreateForm()
    {
        $crawler = $this->client->request('GET', '/users/create');

        $this->assertTrue($this->client->getResponse()->isOk());

        $this->assertEquals(1, $crawler->filter('html:contains("Confirm Password")')->count());


        // Try to register with invalid details.
        $form = $crawler->selectButton('Create new account')->form();

        $form['username'] = 'Admin';
        $form['email'] = 'admin@admin..nl';
        $form['password'] = '';
        $form['password_confirmation'] = '';

        $crawler = $this->client->submit($form);

        $this->assertRedirectedToAction('B302AuthUsersController@create');
        $this->assertSessionHas('error');
        $this->assertHasOldInput();

        // Try to register with existing details.
        $form['username'] = 'Test';
        $form['email'] = 'admin@admin.nl';
        $form['password'] = 'test123';
        $form['password_confirmation'] = 'test123';

        $crawler = $this->client->submit($form);

        $this->assertRedirectedToAction('B302AuthUsersController@create');
        $this->assertSessionHas('error');
        $this->assertHasOldInput();

        // Try to register with right details.
        $form['username'] = 'Test';
        $form['email'] = 'test@test.nl';
        $form['password'] = 'test123';
        $form['password_confirmation'] = 'test123';

        $crawler = $this->client->submit($form);

        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('notice');

        // Check if user is inserted.
        $insertedUser = User::where('email', 'test@test.nl')->first();
        $this->assertNotEmpty($insertedUser->id);
    }

    /**
     * Test the confirmation form.
     */
    public function testConfirmForm()
    {
        $user = User::where('email', $this->unconfirmedUser->email)->first();

        // Do a request with a invalid token.
        $crawler = $this->client->request('GET', '/users/confirm/test12323434234sdfsadfasdfas');

        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('error');

        // Do a request with a valid token.
        $crawler = $this->client->request('GET', '/users/confirm/' . $user->confirmation_code);

        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('notice');

        $user = User::where('email', $this->unconfirmedUser->email)->first();
        $this->assertEquals(1, $user->confirmed);
    }

    /**
     * Test the forgot password forms.
     */
    public function testForgotPasswordForm()
    {
        // Get the password forget form.
        $crawler = $this->client->request('GET', '/users/forgot_password');

        $this->assertTrue($this->client->getResponse()->isOk());

        $this->assertEquals(1, $crawler->filter('html:contains("Email")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Password")')->count());

        // Try to fill in wrong email.
        $form = $crawler->selectButton('Continue')->form();

        $form['email'] = 'test123@admin.nl';

        $crawler = $this->client->submit($form);

        $this->assertSessionHas('error');
        $this->assertRedirectedToAction('B302AuthUsersController@forgotPassword');

        // Try to fill in right email.
        $form['email'] = $this->user->email;

        $crawler = $this->client->submit($form);

        $this->assertSessionHas('notice');
        $this->assertRedirectedToAction('B302AuthUsersController@login');
    }

    /**
     * Test the reset password form.
     */
    public function testResetPasswordForm()
    {
        // Make a token for the user.
        $token = Confide::forgotPassword($this->user->email);
        $this->assertNotEquals(false, $token);

        $crawler = $this->client->request('GET', '/users/reset_password/' . $token);

        $this->assertTrue($this->client->getResponse()->isOk());

         // Check if reset password form is displayed.
        $this->assertEquals(0, $crawler->filter('html:contains("Email")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Password")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Confirm Password")')->count());

        // Fill in the password and try to change it.
        $form = $crawler->selectButton('Continue')->form();
        
        $form['password'] = 'test123';
        $form['password_confirmation'] = 'test123';

        $crawler = $this->client->submit($form);

        $this->assertSessionHas('notice');
        $this->assertRedirectedToAction('B302AuthUsersController@login');

        // checks if the token is deleted
        $deleted = Confide::destroyForgotPasswordToken($token);
        $this->assertFalse($deleted);
    }
}

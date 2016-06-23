<?php

use PiekJ\B302Authentication\User;

class B302AuthenticationTest extends TestCase {

    private $user;
    private $unconfirmedUser;

    /**
     * Set up some default properties
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
     * Test the login form
     */
    public function testLoginForm()
    {
        // do a request to the users/login
        $crawler = $this->client->request('GET', '/users/login');

        // check if the response is Ok
        $this->assertTrue($this->client->getResponse()->isOk());

        // check if login form is displayed
        $this->assertEquals(1, $crawler->filter('html:contains("Login")')->count());

        // fill in the form with wrong details
        $form = $crawler->selectButton('Login')->form();

        $form['email'] = $this->user->email;
        $form['password'] = 'admin';

        // submit the form
        $crawler = $this->client->submit($form);

        // check if the response redirects to the login page with the needed information
        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('error');
        $this->assertHasOldInput();

        // change password to the right password
        $form['password'] = 'test123';

        $crawler = $this->client->submit($form);

        $this->assertRedirectedTo('/');

        Auth::logout();

        // try to login with a unconfirmed user
        $form['email'] = $this->unconfirmedUser->email;
        $form['password'] = 'test123';

        $crawler = $this->client->submit($form);

        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('error');
        $this->assertHasOldInput();
    }

    /**
     * Test the registration form
     */
    public function testCreateForm()
    {
        // do a request to the users/login
        $crawler = $this->client->request('GET', '/users/create');

        // check if the response is Ok
        $this->assertTrue($this->client->getResponse()->isOk());

        // check if login form is displayed
        $this->assertEquals(1, $crawler->filter('html:contains("Confirm Password")')->count());

        // fill in the form with wrong details
        $form = $crawler->selectButton('Create new account')->form();

        $form['username'] = 'Admin';
        $form['email'] = 'admin@admin..nl';
        $form['password'] = '';
        $form['password_confirmation'] = '';

        // submit the form
        $crawler = $this->client->submit($form);

        // check if the response redirects to the create page with the needed information
        $this->assertRedirectedToAction('B302AuthUsersController@create');
        $this->assertSessionHas('error');
        $this->assertHasOldInput();

        // fill in complete with existing details
        $form['username'] = 'Test';
        $form['email'] = 'admin@admin.nl';
        $form['password'] = 'test123';
        $form['password_confirmation'] = 'test123';

        // submit the form
        $crawler = $this->client->submit($form);

        // check if the response redirects to the create page with the needed information
        $this->assertRedirectedToAction('B302AuthUsersController@create');
        $this->assertSessionHas('error');
        $this->assertHasOldInput();

        // fill in complete with the right details
        $form['username'] = 'Test';
        $form['email'] = 'test@test.nl';
        $form['password'] = 'test123';
        $form['password_confirmation'] = 'test123';

        // submit the form
        $crawler = $this->client->submit($form);

        // check if the response redirects to the create page with the needed information
        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('notice');

        // check if user is inserted
        $insertedUser = User::where('email', 'test@test.nl')->first();
        $this->assertNotEmpty($insertedUser->id);
    }

    /**
     * Test the confirmation form
     */
    public function testConfirmForm()
    {
        $user = User::where('email', $this->unconfirmedUser->email)->first();

         // do a request to the users/confirm/{{token}} with wrong activation code
        $crawler = $this->client->request('GET', '/users/confirm/test12323434234sdfsadfasdfas');

        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('error');

        // do a request to the users/confirm/{{token}} with right activation code
        $crawler = $this->client->request('GET', '/users/confirm/' . $user->confirmation_code);

        $this->assertRedirectedToAction('B302AuthUsersController@login');
        $this->assertSessionHas('notice');

        $user = User::where('email', $this->unconfirmedUser->email)->first();
        $this->assertEquals(1, $user->confirmed);
    }

    /**
     * Test the forgot password forms
     */
    public function testForgotPasswordForm()
    {
         // do a request to the users/forgot_password
        $crawler = $this->client->request('GET', '/users/forgot_password');

        // check if the response is Ok
        $this->assertTrue($this->client->getResponse()->isOk());

        // check if login form is displayed
        $this->assertEquals(1, $crawler->filter('html:contains("Email")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Password")')->count());

        // fill in the form with wrong details
        $form = $crawler->selectButton('Continue')->form();

        $form['email'] = 'test123@admin.nl';

        $crawler = $this->client->submit($form);

        $this->assertSessionHas('error');
        $this->assertRedirectedToAction('B302AuthUsersController@forgotPassword');

        // fill in the form with right details
        $form['email'] = $this->user->email;

        $crawler = $this->client->submit($form);

        $this->assertSessionHas('notice');
        $this->assertRedirectedToAction('B302AuthUsersController@login');
    }

    /**
     * Test the reset password form
     */
    public function testResetPasswordForm()
    {
        $token = Confide::forgotPassword($this->user->email);
        $this->assertNotEquals(false, $token);

        $crawler = $this->client->request('GET', '/users/reset_password/' . $token);

        $this->assertTrue($this->client->getResponse()->isOk());

         // check if reset password form is displayed
        $this->assertEquals(0, $crawler->filter('html:contains("Email")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Password")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Confirm Password")')->count());

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

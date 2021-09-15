<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\SSession;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class SessionsController extends Controller
{



    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //$sessions = $this->sessions;
        //return $this->render('sessions.manage', compact('sessions'));
        return 1;
    }

    /**
     * Renders the activation screen
     *
     * @return \Illuminate\Http\Response
     */
    public function activateScreen()
    {
        //
        $sessions = SSession::where('is_active', '1')->orderBy('id', 'DESC')->get();
        $active_session = $this->getSession()->name;
        $active_session_id = $this->getSession()->id;
        $sessionList = ['' => 'Select New Session'];
        if (isset($sessions) && !empty($sessions)) {
            foreach ($sessions as $session) :
                $sessionList[$session->id] = $session->name;
            endforeach;
        }
        return $this->render('core::sessions.activate', compact('sessionList', 'active_session', 'active_session_id'));
    }
    /**
     * Method to activate session
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function activate(Request $request)
    {

        /*$other_sessions= SSession::where('is_active', '1')->get();
        foreach ($other_sessions as $other_session) {
            $other_session->is_active = '0';
            $other_session->save();
        }
        //activate this session
        $current_session = $request->current_session;
        $session = SSession::find($current_session);

        $session->is_active = '1';

        $session->save();*/

        $school = $this->getSchool();

        $school->current_session = $request->current_session;

        $school->save();
        return $this->render([]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function activateSession(Request $request, $id)
    {
        $sessions = $this->sessions;
        return $this->render('core::sessions.manage', compact('sessions'));
    }

    /**
     * @param SessionRequest $request
     * @param SSession $session
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, SSession $session)
    {
        $inputs = request()->all();
        $inputs['school_id'] = $this->getSchool()->id;
        $session = $session->create($inputs);
        // Deactivate previous active session
        /*SSession::where('is_active', '1')->where('id', '<>', $session->id)->update([
            'is_active' => '0'
        ]);*/
        return redirect()->route('sessions.index');
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        try {
            $sessions = $this->sessions;
            $session = SSession::findOrFail($id);
            return $this->render('sessions.manage', compact('sessions', 'session'));
        } catch (ModelNotFoundException $ex) {
            return redirect()->route('sessions.index');
        }
    }

    /**
     * @param SessionRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $session = SSession::findOrFail($id);
            $session->update($request->all());
            return redirect()->route('sessions.index');
        } catch (ModelNotFoundException $ex) {
            return redirect()->route('sessions.index');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleSession($id)
    {
        try {
            $session = SSession::findOrFail($id);

            if ($session->is_active == 1) {
                $session->update([
                    'is_active' => '0'
                ]);
                // Active one session
                SSession::where('is_active', '0')->where('id', '<>', $session->id)->update([
                    'is_active' => '1'
                ]);
                $message = 'deactivated';
            } else {
                $session->update([
                    'is_active' => '1'
                ]);
                // Deactivate previous active session
                SSession::where('is_active', '1')->where('id', '<>', $session->id)->update([
                    'is_active' => '0'
                ]);
                $message = 'activated';
            }
            return redirect()->route('sessions.index');
        } catch (ModelNotFoundException $ex) {
            return redirect()->route('sessions.index');
        }
    }
}

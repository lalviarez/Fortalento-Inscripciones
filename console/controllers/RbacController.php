<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\User;
use yii\helpers\Console;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        

        $this->stdout("*** Todos los datos de autorización y autenticación de usuario serán removidos\n", Console::FG_YELLOW);
        if ($this->confirm("*** ¿Desea continuar?\n"))
        {
            $auth->removeAll();
            if ($user = User::findOne(['username' => 'superadmin@fundacite-merida.gob.ve']))
                $user->delete();

            if ($user = User::findOne(['username' => 'admin@fundacite-merida.gob.ve']))
                $user->delete();
        }
        else {
            return exit(0);
        }

        // Creando los permisos de acceso
        $this->stdout("*** Creando permisos de acceso\n", Console::FG_YELLOW);

        // Agregando acceso a la página de inicio
        $permisoInicio = $auth->createPermission('/site/index');
        $permisoInicio->description = 'Acceso a la página de inicio';
        $auth->add($permisoInicio);

        // Agregando acceso a la página de login
        $permisoLogin = $auth->createPermission('/site/login');
        $permisoLogin->description = 'Acceso a la página de login';
        $auth->add($permisoLogin);

        // Agregando acceso a la página de logout
        $permisoLogout = $auth->createPermission('/site/logout');
        $permisoLogout->description = 'Acceso a la página de logout';
        $auth->add($permisoLogout);

        // Agregando acceso a todo el sitio
        $permisoAll = $auth->createPermission('/*');
        $permisoAll->description = 'Acceso a todo';
        $auth->add($permisoAll);

        $permisoReportesIndex = $auth->createPermission('/reportes/index');
        $permisoReportesIndex->description = 'Acceso a reportes/index';
        $auth->add($permisoReportesIndex);

        $permisoEstudiantesCreate = $auth->createPermission('/estudiantes/create');
        $permisoEstudiantesCreate->description = 'Acceso a estudiantes/create';
        $auth->add($permisoEstudiantesCreate);
        
        $permisoInscripcionesCreate = $auth->createPermission('/inscripciones/create');
        $permisoInscripcionesCreate->description = 'Acceso a inscripciones/create';
        $auth->add($permisoInscripcionesCreate);

        $permisoInscripcionesGetPlanteles = $auth->createPermission('/inscripciones/get-planteles');
        $permisoInscripcionesGetPlanteles->description = 'Acceso a inscripciones/get-planteles';
        $auth->add($permisoInscripcionesGetPlanteles);

        $permisoEstudioSocioEconomicoCreate = $auth->createPermission('/estudio-socio-economico/create');
        $permisoEstudioSocioEconomicoCreate->description = 'Acceso a estudio-socio-economico/create';
        $auth->add($permisoEstudioSocioEconomicoCreate);

        $permisoInscripcionesCerrarEImprimir = $auth->createPermission('/inscripciones/cerrar-e-imprimir');
        $permisoInscripcionesCerrarEImprimir->description = 'Acceso a inscripciones/cerrar-e-imprimir';
        $auth->add($permisoInscripcionesCerrarEImprimir);

        $permisoReportesInscripcion = $auth->createPermission('/reportes/inscripcion');
        $permisoReportesInscripcion->description = 'Acceso a reportes/inscripcion';
        $auth->add($permisoReportesInscripcion);

        $permisoInscripcionesInscripcionCerrada = $auth->createPermission('/inscripciones/inscripcion-cerrada');
        $permisoInscripcionesInscripcionCerrada->description = 'Acceso a inscripciones/inscripcion-cerrada';
        $auth->add($permisoInscripcionesInscripcionCerrada);

        $permisoProcesosProcesoCerrado = $auth->createPermission('/procesos/proceso-cerrado');
        $permisoProcesosProcesoCerrado->description = 'Acceso a procesos/proceso-cerrado';
        $auth->add($permisoProcesosProcesoCerrado);

        $this->stdout("*** Creando datos de superadmin\n", Console::FG_YELLOW);
        // Create role superadmin
        $role = $auth->createRole('superadmin');
        $auth->add($role);
        $auth->addChild($role, $permisoAll);
        
        // Create user superadmin
        $user = new User();
        $user->username = 'superadmin@fundacite-merida.gob.ve';
        $user->email = 'superadmin@fundacite-merida.gob.ve';
        $user->setPassword('123456'); // Este password debe ser cambiado por uno más complejo
        $user->generateAuthKey();
        $user->save(false);

        // Add rol superadmin to user superadmin
        $auth = Yii::$app->authManager;
        $authorRole = $auth->getRole('superadmin');
        $auth->assign($authorRole, $user->getId());

        $this->stdout("*** Creando datos de admin\n", Console::FG_YELLOW);
        // Create role admin
        $role = $auth->createRole('admin');
        $auth->add($role);
        
        // Create user admin
        $user = new User();
        $user->username = 'admin@fundacite-merida.gob.ve';
        $user->email = 'admin@fundacite-merida.gob.ve';
        $user->setPassword('123456'); // Este password debe ser cambiado por uno más complejo
        $user->generateAuthKey();
        $user->save(false);

        // Add rol admin to user admin
        $auth = Yii::$app->authManager;
        $authorRole = $auth->getRole('admin');
        $auth->assign($authorRole, $user->getId());

        $this->stdout("*** Creando rol Estudiantes\n", Console::FG_YELLOW);
        // Create role estudiante
        $role = $auth->createRole('Estudiantes');
        $auth->add($role);
        $auth->addChild($role, $permisoLogout);
        $auth->addChild($role, $permisoReportesIndex);
        $auth->addChild($role, $permisoEstudiantesCreate);
        $auth->addChild($role, $permisoInscripcionesCreate);
        $auth->addChild($role, $permisoInscripcionesGetPlanteles);
        $auth->addChild($role, $permisoEstudioSocioEconomicoCreate);
        $auth->addChild($role, $permisoInscripcionesCerrarEImprimir);
        $auth->addChild($role, $permisoReportesInscripcion);
        $auth->addChild($role, $permisoInscripcionesInscripcionCerrada);
        $auth->addChild($role, $permisoProcesosProcesoCerrado);
    }
}
<?php 
namespace App\Traits;
use App\Models\Permission;
use App\Models\Designation;

trait HasPermissionsTrait{
	// get permissions 
	public function getAllPermissions($permission){
		return Permission::whereIn('slug',$permission)->get();
	}

	// check has permission 
	// public function hasPermission($permission){
	// 	return (bool) $this->permissions->where('slug',$permission->slug)->count();
	// }

	// public function hasPermission($permission_slug){
	// 	foreach ($roles->permissions as $permission) {
	// 		if($this->roles->contains('slug',$role)){
	// 			return true;
	// 		}
	// 	}
	// 	return false;
	// }

	// check has role
	public function hasRole($roles){
		foreach($roles as $role){
			if($this->roles->slug == $role){
				return true;
			}
		}
		return false;
	}

	public function hasPermissionTo($permission){
		return $this->hasPermissionThroughRole($permission);
		// return $this->hasPermissionThroughRole($permission) || $this->hasPermission($permission);
	}

	public function hasPermissionThroughRole($permissions){
		foreach($permissions->roles as $role){
			if($this->roles->slug == $role->slug){
				return true;
			}
		}
		return false;
	}

	// give permission
	public function givePermissionTo(...$permissions){
		$permissions = $this->getAllPermissions($permissions);
		if($permissions == null){
			return $this;
		}
		$this->permissions()->saveMany($permissions);
		return $this;
	}

	public function roles(){
		return $this->belongsTo(Designation::class, 'designation');
	}

}
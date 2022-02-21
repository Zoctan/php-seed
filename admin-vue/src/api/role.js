import axios from '@/utils/axios'

export function listRoleWithPermission(params) {
  return axios({
    url: '/role/permission',
    method: 'get',
    params
  })
}

export function list(params) {
  return axios({
    url: '/role',
    method: 'get',
    params
  })
}

export function listResourcePermission(params) {
  return axios({
    url: '/permission',
    method: 'get',
    params
  })
}

export function add(params) {
  return axios({
    url: '/role',
    method: 'post',
    params
  })
}

export function update(params) {
  return axios({
    url: '/role',
    method: 'put',
    params
  })
}

export function remove(roleId) {
  return axios({
    url: '/role/' + roleId,
    method: 'delete'
  })
}

export function updateAccountRole(params) {
  return axios({
    url: '/account/role',
    method: 'put',
    params
  })
}

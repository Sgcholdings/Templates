import { Injectable } from '@angular/core';

@Injectable()

export class TokenService {

  private iss = {
    login : 'http://localhost:8000/api/login',

    signup : 'http://localhost:8000/api/signup'
  }

  constructor () {}

  handle(token) {
    this.set(token);
  }

  set(token){
    localStorage.setItem('token', token);
  }

  get(){
    return localStorage.getItem('token');
  }

  remove() {
    localStorage.removeItem('token');
  }

  isValid() {
    const token = this.get();
    if(token && 'undefined' != token) {
      const payload = this.payload(token);
      if (payload){
        return Object.values(this.iss).indexOf(payload.iss) > -1 ? true:false;
      }
    }
    return false;
  }

  payload(token){
    console.log(token);
    const payload = token.split('.')[1];
    return this.decode(payload);
  }

  decode(payload) {
    console.log(payload);
    console.log(atob(payload));
    return JSON.parse(atob(payload));
    // return JSON.parse(payload);
  }

  loggedIn() {
    return this.isValid();
  }
}

import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { SnotifyService } from 'ng-snotify';
import { JarwisService } from 'src/app/services/jarwis.service';

@Component({
  selector: 'app-response-reset',
  templateUrl: './response-reset.component.html',
  styleUrls: ['./response-reset.component.css']
})
export class ResponseResetComponent implements OnInit {

  public error=[];

  public form = {
    email : null,
    password : null,
    password_confirmation : null,
    resetToken : null,
  }

  constructor(
    private Jarwis: JarwisService,
    private router: Router,
    private route: ActivatedRoute,
    private Notify:SnotifyService

  ) {
      route.queryParams.subscribe(params => {
      this.form.resetToken = params['token']
    });
  }

  ngOnInit(): void {
  }

  onSubmit() {
    this.Jarwis.changePassword(this.form).subscribe(
      data => this.handleResponse(data),
      error => this.handleError(error)
    )
  }

  handleResponse(data) {

    let _router = this.router

    this.Notify.confirm('Done! Now login with new Password', {
      buttons: [
        { text: 'Okay',
        action:toster=>{
          _router.navigateByUrl('/login'),
          this.Notify.remove(toster.id)
        }
      },
      ]
    })
  }

  handleError(error) {
  this.error = error.error.errors;
  }

}

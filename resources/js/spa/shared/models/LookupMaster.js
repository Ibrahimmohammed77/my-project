export class LookupMaster {
    constructor(data = {}) {
        this.id = data.master_id;
        this.code = data.code;
        this.name = data.name;
        this.description = data.description;
        this.values = data.values || [];
    }

    static fromJson(json) {
        return new LookupMaster(json);
    }
}


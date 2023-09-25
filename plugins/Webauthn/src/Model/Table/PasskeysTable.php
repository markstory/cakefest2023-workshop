<?php
declare(strict_types=1);

namespace App\Webauthn\Model\Table;

use App\Webauthn\Model\CreateData;
use App\Webauthn\Model\Entity\Passkey;
use Cake\Database\Type\JsonType;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Passkeys Model
 *
 * @property \App\Webauthn\Model\Table\UsersTable&\Cake\ORM\Association\BelongsTo $Users
 * @method \App\Webauthn\Model\Entity\Passkey newEmptyEntity()
 * @method \App\Webauthn\Model\Entity\Passkey newEntity(array $data, array $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey[] newEntities(array $data, array $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey get($primaryKey, $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Webauthn\Model\Entity\Passkey[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class PasskeysTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('passkeys');
        $this->setPrimaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->getSchema()->setColumnType('payload', 'json');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('user_id')
            ->notEmptyString('user_id');

        $validator
            ->scalar('credential_id')
            ->requirePresence('credential_id', 'create')
            ->notEmptyString('credential_id');

        $validator
            ->scalar('payload')
            ->requirePresence('payload', 'create')
            ->notEmptyString('payload');

        $validator
            ->scalar('for_login')
            ->boolean('for_login');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('user_id', 'Users'), ['errorField' => 'user_id']);

        return $rules;
    }

    public function createFromData(CreateData $data, string $displayName): Passkey
    {
        $key = $this->newEmptyEntity();
        $key->credential_id = $data->getCredentialId();
        $key->display_name = $displayName;
        $key->payload = $data->getPayload();

        return $key;
    }

    public function findForUser(SelectQuery $query, int $userId): SelectQuery
    {
        return $query->where(['Passkeys.user_id' => $userId]);
    }
}
